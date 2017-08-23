<?php

namespace controllers\jobs;

use \stdClass;
use \Exception;
use PHPHtmlParser\Dom;
use core\Format;
use core\CURL;
use core\Debug;
use core\Validator;
use models;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/17/17
 * @package prunejuice
 */
class ImportBills extends \core\Job {

    /** @var int current session of congress */
    private $session;
    private $sessionOrd;

    /** @var string[] member_id index by bioguide ID */
    private $member_index = [];

    /**
     * Main work function
     */
    protected function doWork() {
        $this->session    = 115;
        $this->sessionOrd = Format::ordSuffix($this->session);
        $page = 1;
        try {

            // build member index
            while ($member = models\Member::findMulti([]))
                $this->member_index[$member->bioguide_id] = $member->member_id;

            // main Page loop
            do {
                $dom = $this->getRecentBills($page);

                /** @var Dom\AbstractNode $li */
                $listItems = $dom->find('li.compact');

                foreach ($listItems as $li) {

                    // Step 1: find doc ID, action date, link
                    // Step 2: check for data and modtime. If data is up to date, exit job since results are sorted by most recent.
                    // Step 3: Save the bill and continue

                    /** @var Dom\AbstractNode $aHeading */
                    if (!$aHeading = $li->find('.result-heading a', 0))
                        continue;

                    $title = $li->find('.result-title', 0);

                    $bill = models\Bill::new([
                        'code'    => trim($aHeading->text()),
                        'title'   => $title ? $title->text() : null,
                        'session' => $this->session,
                        'link'    => $aHeading->getAttribute('href'),
                    ])->validate();

                    $bill->lastActionDate = $this->getLastActionDate($li);

                    // Pull down the Bill and save it.
                    // If this returns false, assume we are done processing the job because everything is up to date.
                    if (!$this->saveBillData($bill))
                        return;

                }

                /**
                 * Determine if we should continue iteration
                 * @var Dom\AbstractNode $next */
                $next    = $dom->find('.pagination .next', 0);
                $hasNext = $next && substr_count($next->getAttribute('class'), 'off') === 0;

            } while ($hasNext && $page++);

        } catch (Exception $e) {
            // Fatal error. Stop job and exit
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
            return;
        }
    }

    /**
     * Fetches a list of all Bills in the current legislation
     * @throws Exception
     * @return Dom
     */
    private function getRecentBills($page) {

        $url = "http://www.congress.gov/search";
        $url .= '?q=' . json_encode(['source' => 'legislation', 'congress' => $this->session]);
        $url .= '&' . http_build_query([
                'pageSort' => 'latestAction:desc',
                'pageSize' => 250,
                'page'     => $page,
            ]);

        Debug::info(__METHOD__ . " Fetching page $url");

        $curl = Curl::new($url);
        $html = $curl->exec();
        if ($curl->code() >= 400)
            throw new Exception("$url&page=$page Failed");

        $dom = new Dom();
        $dom->load($html, [
            'whitespaceTextNode' => false,
            'removeScripts'      => true,
            'removeStyles'       => true,
        ]);

        return $dom;
    }

    /**
     * Parses a search page line item for the date of the last action
     * @param Dom\AbstractNode $node
     * @return int
     */
    private function getLastActionDate(Dom\AbstractNode $node) {
        try {
            foreach ($node->find('.result-item') as $rItem) {
                if (!$strong = $rItem->find('strong', 0))
                    continue;

                if (substr(strtolower($strong->text()), 0, 13) != 'latest action')
                    continue;

                if (!$actionText = $rItem->find('span', 0))
                    continue;

                if (!preg_match('@\d{2}/\d{2}/\d{2,4}@mis', $actionText->text(), $matches))
                    continue;

                return strtotime($matches[0]);
            }
        } catch (Exception $e) {
            Debug::info(__METHOD__ . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Downloads and saves the Bill data.
     * If the bill has not been updated, returns false
     * @param models\Bill $bill
     * @return bool
     */
    public function saveBillData(models\Bill $bill) {
        try {
            Debug::info("Pulling Bill $bill->bill_id");
            Debug::info('memory:', (memory_get_usage() / 1024 / 1024));

            // If we have the most recent bill data, return early
            $lastAction = models\BillAction::findOne(['bill_id' => $bill->bill_id], ['sort' => ['index' => -1]]);
            if ($lastAction && strtotime($lastAction->date) >= strtotime($bill->lastActionDate))
                return true;


            $url  = preg_replace('/\?.*$/s', '', $bill->link) . '/all-info';
            $curl = new CURL($url);
            $html = $curl->exec();
            if ($curl->code() > 400)
                throw new Exception($curl->code() . " - $url");

            $dom = new Dom();
            $dom->load($html, [
                'whitespaceTextNode' => false,
                'removeScripts'      => true,
                'removeStyles'       => true,
            ]);

            if ($element = $dom->find('#titles_main .house p', 0))
                $bill->title_house = $element->text();

            if ($element = $dom->find('#titles_main .senate p', 0))
                $bill->title_senate = $element->text();

            if ($element = $dom->find('#latestSummary-content [role=main]', 0))
                $bill->summary = $element->innerHtml();

            // get Summary data
            $this->parseHeading($dom, $bill);
            Debug::info("Memory after parseHeading", (memory_get_usage() / 1024 / 1024));
            $this->parseActions($dom, $bill);
            Debug::info("Memory after parseActions", (memory_get_usage() / 1024 / 1024));
            $this->parseCosponsors($dom, $bill);
            Debug::info("Memory after parseCosponsors", (memory_get_usage() / 1024 / 1024));

            unset($dom);
            $bill->save();
            Debug::info("Memory after save", (memory_get_usage() / 1024 / 1024));

            $this->parseDocument($bill);
            Debug::info("Memory after parseDocument", (memory_get_usage() / 1024 / 1024));

            return true;

        } catch (Exception $e) {
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * @param $url
     * @return mixed
     * @example https://www.congress.gov/member/susan-collins/C001035
     */
    private function getMemberIdFromURL($url) {
        $path = explode('/', parse_url($url, PHP_URL_PATH));
        return @$this->member_index[array_pop($path)];
    }

    /**
     * @param Dom $dom
     * @param models\Bill $bill
     */
    private function parseHeading(Dom $dom, models\Bill $bill) {
        try {
            /** @var Dom\AbstractNode[] $rows */
            $rows = $dom->find('.overview tr');
            foreach ($rows as $row) {
                if (!$label = $row->find('th', 0))
                    continue;

                $label = mb_strtolower(trim($label->text(), ':'));

                switch ($label) {
                    case 'amends bill':
                        if (!$a = $row->find('a', 0))
                            continue;

                        if (!preg_match('@/(\d+)\w\w-congress@mis', $a->getAttribute('href'), $matches))
                            continue;

                        $session           = $matches[1];
                        $code              = $a->text();
                        $bill->amends_bill = models\Bill::calculateId($code, $session);
                        break;

                    case 'sponsor':
                        if (!$a = $row->find('a', 0))
                            continue;

                        $bill->sponsor = $this->getMemberIdFromURL($a->getAttribute('href'));
                        break;
                }


            }

        } catch (Exception $e) {
            Debug::info(__METHOD__ . ": " . $e->getMessage());
        }
    }

    /**
     * @param Dom $dom
     * @param models\Bill $bill
     */
    private function parseActions(Dom $dom, models\Bill $bill) {
        try {
            $tableRows   = $dom->find('#allActions-content tbody tr');
            $billActions = [];

            $total = count($tableRows);

            /** @var Dom\AbstractNode $tr */
            foreach ($tableRows as $i => $tr) {
                /** @var Dom\AbstractNode[] $cells */
                $cells       = $tr->find('td');
                $dateCell    = $tr->find('td.date', 0);
                $noteCell    = $tr->find('td.actions', 0);
                $chamberCell = count($cells) > 2 ? $cells[1] : null;

                if (!isset($dateCell, $noteCell))
                    continue;

                try {
                    $billActions[] = models\BillAction::new([
                        'bill_id' => $bill->bill_id,
                        'index'   => ($total - $i),
                        'date'    => date('Y-m-d', strtotime($dateCell->text())),
                        'chamber' => $chamberCell ? $chamberCell->text() : null,
                        'note'    => $noteCell->text(),
                    ])->validate();

                } catch (Validator $v) {
                    Debug::error(__METHOD__ . ': ' . $v->getMessage());
                    continue;
                }

            }

            if (count($billActions))
                models\BillAction::insertMulti($billActions, true);

        } catch (Exception $e) {
            Debug::info(__METHOD__ . ": " . $e->getMessage());
        }

    }

    /**
     * @param Dom $dom
     * @param models\Bill $bill
     * @return array[]
     * cosponsors-content
     */
    private function parseCosponsors(Dom $dom, models\Bill $bill) {
        $billCosponsors = [];
        try {
            $tableRows = $dom->find('#cosponsors-content tbody tr');

            /** @var Dom\AbstractNode $tr */
            foreach ($tableRows as $tr) {
                $cells = $tr->find('td');

                if (!$firstAnchor = $cells[0]->find('a', 0))
                    continue;

                if (!$memberUrl = $firstAnchor->getAttribute('href'))
                    continue;

                $billCosponsors[] = models\BillCosponsor::new([
                    'bill_id'   => $bill->bill_id,
                    'member_id' => $this->getMemberIdFromURL($memberUrl),
                    'date'      => $cells[1] ? date('Y-m-d', strtotime($cells[1]->text())) : null,
                ])->validate();
            }

            if (count($billCosponsors))
                models\BillCosponsor::insertMulti($billCosponsors, true);

        } catch (Exception $e) {
            Debug::info(__METHOD__ . ": " . $e->getMessage());
        }

    }

    /**
     * Pulls the written bill itself and stores it in the table
     * @param models\Bill $bill
     */
    private function parseDocument(models\Bill $bill) {
        try {
            $url = preg_replace('/\?.*$/s', '', $bill->link) . '/text';

            Debug::info(__METHOD__ . " Fetching page $url");

            $curl = Curl::new($url);
            $html = $curl->exec();
            if ($curl->code() >= 400)
                throw new Exception("$url Failed");

            $dom = new Dom();
            $dom->load($html, [
                'whitespaceTextNode' => false,
                'removeScripts'      => true,
                'removeStyles'       => true,
            ]);

            $document_full = null;

            /** @var Dom\AbstractNode $body */
            if ($body = $dom->find('.generated-html-container body', 0))
                $document_full = $body->outerHtml();

            /** @var Dom\AbstractNode $pre */
            if (!$document_full && $pre = $dom->find('pre', 0))
                $document_full = $pre->outerHtml();

            // don't attach document to the bill in order to save memory
            if ($document_full)
                $bill->update(['document_full' => $document_full]);

            $body && $body->delete();
            $pre && $pre->delete();
            unset($dom, $body, $pre);

        } catch (Exception $e) {
            Debug::info(__METHOD__ . ": " . $e->getMessage());
        }
    }

}