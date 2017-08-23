<?php

namespace models;

use core;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/21/17
 * @package politics
 */
class MemberCommittee extends core\Model {
    const TABLE = 'member_committee';
    const INDEX = 'member_committee_id';

    use core\db\traits\Mysql;

    public $member_committee_id;
    public $member_id;
    public $committee_id;

    public static function calculateId($member_id, $committee_id) {
        return md5($member_id . $committee_id);
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