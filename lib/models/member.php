<?php

namespace models;

use core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package politics
 */
class Member extends core\Model {
    const TABLE = 'member';
    const INDEX = 'member_id';

    use core\db\traits\Mysql;

    public $member_id;
    public $first_name;
    public $last_name;
    public $full_name;
    public $bioguide_id;
    public $thomas_id;
    public $govtrack_id;
    public $opensecrets_id;
    public $votesmart_id;
    public $cspan_id;
    public $wikipedia_id;
    public $house_history_id;
    public $ballotpedia_id;
    public $maplight_id;
    public $icpsr_id;
    public $wikidata_id;
    public $google_entity_id;
    public $twitter_id;
    public $instagram_id;
    public $facebook_id;
    public $youtube_id;
    public $gender;
    public $religion;
    public $date_of_birth;

    /**
     * @param $full_name
     * @param $date_of_birth
     * @return string
     */
    public static function calculateId($full_name, $date_of_birth) {
        $name_std = mb_strtolower(preg_replace('/[^\w\d]+/', '-', $full_name));
        return md5($name_std . $date_of_birth);
    }

    /**
     * Validates the object
     * @return self
     */
    public function validate() {
        $v = new core\Validator();
        $v->done("\n");
        return $this;
    }

    /**
     * Formats & standardizes the object
     * @return self
     */
    public function format() {
        return $this;
    }

    /**
     * @param array $terms
     */
    public function setTerms(array $terms) {
        MemberTerm::deleteMulti(['member_id' => $this->member_id]);
        foreach ($terms as &$term)
            $term = MemberTerm::new($term)->setVars(['member_id' => $this->member_id]);

        MemberTerm::insertMulti($terms);
    }

    /** @return mixed */
    public function getCurrentTerm() {
        return $this->_currentTerm ?? $this->_currentTerm = MemberTerm::findOne(['member_id' => $this->member_id], ['sort' => ['start' => -1]]);
    }

    /** @return string */
    public function getOffice() {
        return $this->getCurrentTerm()->type;
    }

    /** @return bool */
    public function isSenator() {
        return $this->getOffice() === 'sen';
    }

    /** @return bool */
    public function isRepresentative() {
        return $this->getOffice() === 'rep';
    }

    /** @return bool */
    public function isInOffice() {
        $now = date('Ymd');
        $term = $this->getCurrentTerm();
        $start = str_replace('-', '', $term->start);
        $end   = str_replace('-', '', $term->end);
        return $now >= $start && $now <= $end;
    }

//    /**
//     * Generates an index entry intended for congress.json
//     * @return array
//     */
//    public function getIndexEntry() {
//        return [
//            'id' => $this->id,
//            'name' => $this->name->official_full ?? implode(' ', (array)$this->name),
//            'party' => $this->getCurrentTerm()->party,
//            'office' => $this->getOffice(),
//            'state' => $this->getCurrentTerm()->state ?? null,
//            'district' => $this->getCurrentTerm()->district ?? null,
//        ];
//    }

}