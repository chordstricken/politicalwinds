<?php

namespace models;

use core;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/22/17
 * @package politics
 */
class BillCosponsor extends core\Model {
    const TABLE = 'bill_cosponsor';
    const INDEX = 'bill_cosponsor_id';

    use core\db\traits\Mysql;

    public $bill_cosponsor_id;
    public $bill_id;
    public $member_id;
    public $date;


    /**
     * @param $bill_id
     * @param $member_id
     * @return string
     */
    public static function calculateId($bill_id, $member_id) {
        return md5($bill_id . $member_id);
    }

    /**
     * Validates the object
     * @return self
     */
    public function validate() {
        $v = new core\Validator();
        $v->check_text($this->bill_id, 'bill_id', 'Bill Id', 1, 127, true);
        $v->check_text($this->member_id, 'member_id', 'Member Id', 1, 127, true);
        $v->check_date($this->date, 'date', 'Date', false);
        $v->done("\n");

        if (!isset($this->bill_cosponsor_id))
            $this->bill_cosponsor_id = self::calculateId($this->bill_id, $this->member_id);

        return $this;
    }

}