<?php

namespace controllers\jobs;

use \Exception;
use core\CURL;
use core\Debug;
use models\Domain;

use PHPHtmlParser\Dom;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/12/17
 * @package prunejuice
 */
class Scrape extends \core\Job {

    /**
     * Main work function
     */
    protected function doWork() {

        if (!isset($this->params->query))
            $this->params->query = ['scrape.lastUpdated' => ['$eq' => null]];

        if (!isset($this->domains))
            $this->domains = Domain::findMulti($this->params->query);

        foreach ($this->domains as $domain) {

            Debug::info("Starting Scrape $domain->name");

            $this->parseHomepage($domain);

            try {
                $domain->update([
                    'resolvedUrl'  => $domain->resolvedUrl,
                    'scrape'       => $domain->scrape,
                    'dateModified' => $domain->dateModified,
                ]);
            } catch (Exception $e) {
                Debug::error($e);
            }

            Debug::info("Finished Scrape $domain->name");
        }
    }

    /**
     * Pulls the main homepage down
     * Checks for copyright, and stores resolved URL
     * @param Domain $domain
     */
    private function parseHomepage(Domain $domain) {
        Debug::info(__METHOD__ . " $domain->name");

        $curl = CURL::new("http://$domain->name")->setOpt(CURLOPT_MAXREDIRS, 10);
        $page = $curl->exec();

        preg_match_all('/(©| C |&copy;|&#169;|copyright) *([\d\-]+)/mis', $page, $matches);
        $domain->resolvedUrl       = $curl->getResolvedURL();
        $domain->scrape->copyright = $matches[2][0] ?? null;

        // find the scraped lastUpdated time.
        $dates = $this->scrapeDates($page);
        if (!count($dates))
            $dates = $this->parseBlogDates($domain);

        $now = time();
        foreach ($dates as $time => $date) {
            if ($time < $now && $time > $domain->scrape->lastUpdate) {
                $domain->scrape->lastUpdate = $time;
                $domain->scrape->page = $curl->getResolvedURL();
            }
        }
    }

    /**
     * Pulls down blog page variants and checks for timestamps
     * @param Domain $domain
     * @return array
     */
    private function parseBlogDates(Domain $domain) {
        Debug::info(__METHOD__ . " $domain->name");
        $paths = [
            'blog',
            'news',
            'articles',
            'musings',
        ];

        foreach ($paths as $path) {
            $curl = CURL::new("http://$domain->name/$path");
            $html = $curl->exec();

            if ($curl->code() < 400) {
                $dates = $this->scrapeDates($html);
                if (count($dates))
                    return $dates;
            }
        }

        return [];
    }

    /**
     * Scrapes all date strings from the page
     * @param $html
     * @return string[]
     */
    private function scrapeDates($html) {

        $dates = [];

        $dom = new Dom();
        $dom->load($html, [
            'whitespaceTextNode' => false,
            'removeScripts'      => true,
            'removeStyles'       => true,
        ]);
        $html = $dom->innerHtml;
        unset($dom);

        $monthRegex = '(jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec|january|february|march|april|june|july|august|september|october|november|december)';

        $dateRexp = [
            "@$monthRegex \d+,?( \d\d\d\d)?@mis", // May 12, 2017 or May 12
            "@[1-3]?[0-9]+ $monthRegex \d+@mis", // 28 February 2017
            "@[0-1]?[0-9]/[1-3]?[0-9]/\d{2,4}@mis", // 12/31/88 or 12/31/1988
            "@\d{4}-[0-1][0-9]-[0-3][0-9]@mis", // 2017-07-14
        ];

        foreach ($dateRexp as $rexp) {
            if (preg_match_all($rexp, $html, $results)) {
                Debug::info("Dates: " . implode(', ', $results[0]));
                $dates = array_merge($dates, $results[0]);
            }
        }

        // convert dates into assoc array and sort based on timestamp, descending
        $dates = array_unique($dates);
        $dateMap = [];
        foreach ($dates as $date) {
            $dateMap[strtotime($date)] = $date;
        }
        krsort($dateMap);

        return $dateMap;

    }

}