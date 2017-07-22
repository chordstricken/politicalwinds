<?php

namespace controllers\jobs;

use Symfony\Component\Yaml\Yaml;

use \Exception;
use core\CURL;
use core\Debug;
use models\Member;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package prunejuice
 */
class importMembers extends \core\Job {

    /** @var Member[] */
    private $members = [];

    /** @var Member[] */
    private $congressIndex = [];

    /**
     * Main work function
     */
    protected function doWork() {
        $this->getExecutive();
        $this->getCongress();
//        $this->getCongressHistory();

        $this->getSocialMedia();
        $this->getCommittees();
        $this->getCommitteeMemberships();

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

        if (file_exists("$cacheDir/$path")) {
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
     * @return Member
     */
    private function addMember($data) {
        $id = $data->id->bioguide;

        // rename 'id' to 'appIds'
        $data->appIds = (object)$data->id;
        $data->id = $id;

        if (!isset($this->members[$id]))
            $this->members[$id] = new Member($data);
        else
            $this->members[$id]->setVars($data);

        return $this->members[$id];
    }

    /**
     * Pulls YAML data from github
     */
    private function getExecutive() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('executive.yaml');
        array_map([$this, 'addMember'], $data);
    }

    /**
     * Pulls YAML data from github
     */
    private function getCongress() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('legislators-current.yaml');
        foreach ($data as $row) {
            $member = $this->addMember($row);
            $this->congressIndex[$member->id] = null;
        }

        print_r(array_keys($this->congressIndex));
    }

    /**
     * Pulls YAML data from github
     */
    private function getCongressHistory() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('legislators-historical.yaml');
        array_map([$this, 'addMember'], $data);
    }

    /**
     * Pulls YAML data from github
     */
    private function getSocialMedia() {
        Debug::info(__METHOD__ . ': Getting Data');

        $data = $this->getData('legislators-social-media.yaml');
        array_map([$this, 'addMember'], $data);
    }

    private function getCommittees() {
        // committees-current
    }

    private function getCommitteeMemberships() {
        // committee-membership-current
    }

}