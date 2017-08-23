<?php

namespace controllers\jobs;

use Symfony\Component\Yaml\Yaml;

use \Exception;
use core\CURL;
use core\Git;
use core\Debug;
use models;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package prunejuice
 */
class ImportMembers extends \core\Job {

    const REPO_URL  = 'https://github.com/unitedstates/congress-legislators';
    const REPO_PATH = '/unitedstates/congress-legislators';

    /** @var models\Member[] */
    private $members = [];
    /** @var models\MemberTerm[]  */
    private $member_terms = [];

    /** @var array */
    private $member_index = [];

    /**
     * Main work function
     */
    protected function doWork() {
        try {
            $oldHead = Git::head(self::REPO_PATH);
            $result  = Git::pull(self::REPO_URL, self::REPO_PATH);
            $newHead = Git::head(self::REPO_PATH);

            if ($newHead['Hash'] === $oldHead['Hash']) {
                Debug::info(__METHOD__ . " No new updates");
                return;
            }

            $this->getExecutive();
            $this->getCongress();
            $this->getCongressHistory();
            $this->getSocialMedia();
            $this->getCommittees();
            $this->getCommitteeMembership();

            models\Member::insertMulti($this->members, true);
            models\MemberTerm::insertMulti($this->member_terms, true);

        } catch (Exception $e) {
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    /**
     * @param $path
     * @return mixed
     */
    private function getData($path) {
        return Yaml::parse(file_get_contents(ROOT . "/repos/unitedstates/congress-legislators/$path"), Yaml::PARSE_OBJECT_FOR_MAP);
    }

    /**
     * @param $field
     * @param $value
     * @return string|null
     */
    private function getMemberIdFrom($field, $value) {
        return @$this->member_index["$field.$value"] ?? null;
    }

    /**
     * Adds a member to the database
     * @param object $ext Yaml Member Row
     * @return null
     */
    private function addMember($ext) {
        if (!isset($ext->id))
            return null;

        if (isset($ext->name, $ext->bio)) {
            $name = $ext->name->official_full ?? implode(' ', (array)$ext->name);
            $mId  = models\Member::calculateId($name, @$ext->bio->birthday);
        }

        if (!isset($mId) && isset($ext->id->bioguide))
            $mId = $this->getMemberIdFrom('bioguide_id', $ext->id->bioguide);

        if (!isset($mId) && isset($ext->id->govtrack))
            $mId = $this->getMemberIdFrom('govtrack_id', $ext->id->govtrack);

        if (!isset($mId))
            return Debug::error(__METHOD__ . " Failed to find Member. " . json_encode($ext));

        $m = $this->members[$mId] ?? $this->members[$mId] = new models\Member(['member_id' => $mId]);

        if (isset($ext->id)) {
            $m->bioguide_id      = $ext->id->bioguide ?? $m->bioguide_id;
            $m->thomas_id        = $ext->id->thomas ?? $m->thomas_id;
            $m->govtrack_id      = $ext->id->govtrack ?? $m->govtrack_id;
            $m->opensecrets_id   = $ext->id->opensecrets ?? $m->opensecrets_id;
            $m->votesmart_id     = $ext->id->votesmart ?? $m->votesmart_id;
            $m->cspan_id         = $ext->id->cspan ?? $m->cspan_id;
            $m->wikipedia_id     = $ext->id->wikipedia ?? $m->wikipedia_id;
            $m->house_history_id = $ext->id->house_history ?? $m->house_history_id;
            $m->ballotpedia_id   = $ext->id->ballotpedia ?? $m->ballotpedia_id;
            $m->maplight_id      = $ext->id->maplight ?? $m->maplight_id;
            $m->icpsr_id         = $ext->id->icpsr ?? $m->icpsr_id;
            $m->wikidata_id      = $ext->id->wikidata ?? $m->wikidata_id;
            $m->google_entity_id = $ext->id->google_entity_id ?? $m->google_entity_id;

            // set indexes
            if ($m->bioguide_id) $this->member_index["bioguide_id.$m->bioguide_id"] = $mId;
            if ($m->govtrack_id) $this->member_index["govtrack_id.$m->govtrack_id"] = $mId;
        }

        if (isset($ext->name)) {
            $m->first_name = $ext->name->first ?? $m->first_name;
            $m->last_name  = $ext->name->last ?? $m->last_name;
            $m->full_name  = $ext->name->official_full ?? trim("$m->first_name $m->last_name");
        }

        if (isset($ext->bio)) {
            $m->date_of_birth = $ext->bio->birthday ?? $m->date_of_birth;
            $m->gender        = $ext->bio->gender ?? $m->gender;
            $m->religion      = $ext->bio->religion ?? $m->religion;
        }

        if (isset($ext->social)) {
            $m->twitter_id   = $ext->social->twitter_id ?? $ext->social->twitter ?? $m->twitter_id;
            $m->instagram_id = $ext->social->instagram_id ?? $ext->social->instagram ?? $m->instagram_id;
            $m->facebook_id  = $ext->social->facebook_id ?? $ext->social->facebook ?? $m->facebook_id;
            $m->youtube_id   = $ext->social->youtube_id ?? $ext->social->youtube ?? $m->youtube_id;
        }

        if (isset($ext->terms))
            foreach ($ext->terms as $term)
                $this->member_terms[] = models\MemberTerm::new($term)->setVars(['member_term_id' => md5($mId.$term->start.$term->type), 'member_id' => $mId]);

    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getExecutive() {
        Debug::info(__METHOD__ . ': Getting Data');
        $startTime = microtime(true);

        $data = $this->getData('executive.yaml');
        array_map([$this, 'addMember'], $data);
        Debug::info(__METHOD__ . ': Finished in ' . number_format(microtime(true) - $startTime, 4) . ' sec');
    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getCongress() {
        Debug::info(__METHOD__ . ': Getting Data');
        $startTime = microtime(true);

        $data = $this->getData('legislators-current.yaml');
        array_map([$this, 'addMember'], $data);
        Debug::info(__METHOD__ . ': Finished in ' . number_format(microtime(true) - $startTime, 4) . ' sec');
    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getCongressHistory() {
        Debug::info(__METHOD__ . ': Getting Data');
        $startTime = microtime(true);

        $data = $this->getData('legislators-historical.yaml');
        array_map([$this, 'addMember'], $data);
        Debug::info(__METHOD__ . ': Finished in ' . number_format(microtime(true) - $startTime, 4) . ' sec');
    }

    /**
     * Pulls YAML data from theunitedstates.io
     */
    private function getSocialMedia() {
        Debug::info(__METHOD__ . ': Getting Data');
        $startTime = microtime(true);

        $data = $this->getData('legislators-social-media.yaml');
        array_map([$this, 'addMember'], $data);
        Debug::info(__METHOD__ . ': Finished in ' . number_format(microtime(true) - $startTime, 4) . ' sec');
    }

    /**
     * Pulls YAML data from theunitedstates.io and saves it
     */
    private function getCommittees() {
        Debug::info(__METHOD__ . ': Getting Data');
        $startTime = microtime(true);

        $cData      = $this->getData('committees-current.yaml');
        $committees = [];

        // add committees and subcommittees
        foreach ($cData as $row) {
            $row->committee_id = $row->thomas_id;
            $committees[]      = models\Committee::new($row);

            // if there are subs, index by thomas_id
            if (isset($row->subcommittees))
                foreach ($row->subcommittees as $sub)
                    $committees[] = models\Committee::new($row)->setVars($sub)->setVars(['committee_id' => $row->thomas_id . $sub->thomas_id, 'parent' => $row->thomas_id]);
        }

        models\Committee::insertMulti($committees, true);
        Debug::info(__METHOD__ . ': Finished in ' . number_format(microtime(true) - $startTime, 4) . ' sec');
    }

    /**
     * Pulls YAML data from theunitedstates.io and saves it
     */
    private function getCommitteeMembership() {
        Debug::info(__METHOD__ . ': Getting Data');
        $startTime = microtime(true);

        $cmData = $this->getData('committee-membership-current.yaml');
        $m_c    = [];
        foreach ($cmData as $cIdFull => $cMembers) {
            foreach ($cMembers as $cMember) {
                $mId   = $this->getMemberIdFrom('bioguide_id', $cMember->bioguide);
                $m_c[] = models\MemberCommittee::new([
                    'member_committee_id' => models\MemberCommittee::calculateId($mId, $cIdFull),
                    'member_id'           => $mId,
                    'committee_id'        => $cIdFull,
                ]);
            }
        }

        models\MemberCommittee::insertMulti($m_c, true);
        Debug::info(__METHOD__ . ': Finished in ' . number_format(microtime(true) - $startTime, 4) . ' sec');
    }

}