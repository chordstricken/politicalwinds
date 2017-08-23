<?php

namespace models;

use core;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/18/17
 * @package politics
 */
class MemberTerm extends core\Model {
    const TABLE = 'member_term';
    const INDEX = 'member_term_id';

    use core\db\traits\Mysql;

    public $member_term_id; // primary key
    public $member_id;      // PW unique string
    public $start;          // 2017-01-03
    public $end;            // 2005-01-20
    public $how;            // election
    public $party;          // Republican
    public $type;           // prez|viceprez|
    public $address;        // 1218 Longworth HOB; Washington DC 20515-0307
    public $district;       // number
    public $office;         // 1218 Longworth House Office Building
    public $phone;          // 202-225-4065
    public $state;          // AZ
    public $url;            // https://rubengallego.house.gov

    /**
     * @param $member_id
     * @param $start
     * @param $type
     * @return string
     */
    public static function calculateId($member_id, $start, $type) {
        return md5($member_id . $start . $type);
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

}