<?php

namespace controllers\jobs;

use Symfony\Component\Yaml\Yaml;

use \Exception;
use core\CURL;
use core\Debug;
use models;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package prunejuice
 */
class ImportMembers extends \core\Job {

    /** @var models\Member[] */
    private $members = [];

    /** @var models\Member[] */
    private $congressIndex = [];

    /**
     * Main work function
     */
    protected function doWork() {
        $this->getExecutive();
        $this->getCongress();
        $this->getSocialMedia();
        $this->getCommittees();

        foreach ($this->members as $member)
            $member->save();

        foreach ($this->congressIndex as $memberId => &$index)
            $index = $this->members[$memberId]->getIndexEntry();

        $indexFile = ROOT . '/api/static/us/congress.json';
        @mkdir(dirname($indexFile), 0777, true);
        @file_put_contents($indexFile, json_encode($this->congressIndex));
        @chmod($indexFile, 0777);


    }

    /**
     * @param $path
     * @return mixed
     */
    private function getData($path) {
        $cacheDir = ROOT . '/cache';
        if (!is_dir($cacheDir))
            mkdir($cacheDir, 0777, true);

        if (file_exists("$cacheDir/$path") && filemtime("$cacheDir/$path") > strtotime('-1 day')) {
            Debug::info("Cache Hit $path");
            return Yaml::parse(file_get_contents("$cacheDir/$path"), Yaml::PARSE_OBJECT_FOR_MAP);
        } else {
            Debug::info("Cache Miss $path");
        }

        $curl = CURL::new("https://theunitedstates.io/congress-legislators/$path");
        $curl->setOpt(CURLOPT_TIMEOUT, 60);
        $yaml = $curl->exec();

        file_put_contents("$cacheDir/$path", $yaml);

        return Yaml::parse($yaml, Yaml::PARSE_OBJECT_FOR_MAP);
    }

    /**
     * Adds a member to the array
     * @param $memberRow
     * @return models\Member
     */
    private function addMember($data) {
        $id = $data->id->bioguide ?? $data->id->govtrack;

        // rename 'id' to 'appIds'
        $data->appIds = (object)$data->id;
        $data->id = $id;

        if (!isset($this->members[$id]))
            $this->members[$id] = new models\Member($data);
        else
            $this->members[$id]->setVars($data);

        return $this->members[$id];
    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getExecutive() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('executive.yaml');
        foreach ($data as $row) {
            $member = $this->addMember($row);
            if ($member->isInOffice())
                $this->congressIndex[$member->id] = null;
        }
    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getCongress() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('legislators-current.yaml');
        foreach ($data as $row) {
            $member = $this->addMember($row);
            $this->congressIndex[$member->id] = null;
        }

    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getCongressHistory() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('legislators-historical.yaml');
        array_map([$this, 'addMember'], $data);
    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getSocialMedia() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('legislators-social-media.yaml');
        array_map([$this, 'addMember'], $data);
    }

    /**
     * Pulls YAML data from theunitedstates.io and saves it
     */
    private function getCommittees() {
        Debug::info(__METHOD__ . ': Getting Data');

        $cData  = $this->getData('committees-current.yaml');
        $cmData = $this->getData('committee-membership-current.yaml');
        $committees = [];

        foreach ($cData as $row) {
            $row->id = $row->thomas_id;

            // if there are subs, index by thomas_id
            if (isset($row->subcommittees)) {
                $subs = [];
                foreach ($row->subcommittees as $sub)
                    $subs[$sub->thomas_id] = $sub;

                $row->subcommittees = $subs;
            }


            $committees[$row->id] = new models\Committee((array)$row);
        }

        foreach ($cmData as $cIdFull => $cMembers) {
            $scId = null; // assume not a subcommittee
            $cId  = $cIdFull;

            // break apart subcommittee if it exists
            if (strlen($cIdFull) > 4)
                list($cId, $scId) = [substr($cIdFull, 0, 4), substr($cIdFull, 4)];

            if (!isset($committees[$cId]) && !isset($committees[$cIdFull])) {
                Debug::info("Committee $cIdFull not found.");
                continue;
            }

            if (!is_array($committees[$cId]->members))
                $committees[$cId]->members = [];

            foreach ($cMembers as $mData) {
                $mId = $mData->bioguide;

                // store member in the committee
                $committees[$cId]->members[$mId] = $mData->name;

                if (!isset($this->members[$mId])) {
                    Debug::info(__METHOD__ . " member $mId not found");
                    continue;
                }

                // store committee in member
                if (!is_array($this->members[$mId]->committees))
                    $this->members[$mId]->committees = [];

                $this->members[$mId]->committees[$cIdFull] = $committees[$cId]->subcommittees[$scId]->name ?? $committees[$cId]->name ?? 'N/A';

            }
        }

         foreach ($committees as $c)
            $c->save();

    }

}