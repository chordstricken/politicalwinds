<?php

namespace models;

use core;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/22/17
 * @package politics
 */
class BillCommittee extends core\Model {
    const TABLE = 'bill_committee';
    const INDEX = 'bill_committee_id';

    use core\db\traits\Mysql;

    public $bill_committee_id;
    public $bill_id;
    public $committee_id;
    public $date;

    /**
     * @param $bill_id
     * @param $committee_id
     * @return string
     */
    public static function calculateId($bill_id, $committee_id) {
        return md5($bill_id . $committee_id);
    }

    /**
     * Validates the object
     * @return self
     */
    public function validate() {
        $v = new core\Validator();
        $v->check_text($this->bill_id, 'bill_id', 'Bill Id', 1, 127, true);
        $v->check_text($this->committee_id, 'committee_id', 'Committee Id', 1, 127, true);
        $v->check_date($this->date, 'date', 'Date', false);
        $v->done("\n");

        if (!isset($this->bill_committee_id))
            $this->bill_committee_id = self::calculateId($this->bill_id, $this->committee_id);

        return $this;
    }

}