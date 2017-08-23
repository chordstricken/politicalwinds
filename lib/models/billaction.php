<?php

namespace models;

use core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/22/17
 * @package politics
 */
class BillAction extends core\Model {
    const TABLE = 'bill_action';
    const INDEX = 'bill_action_id';

    use core\db\traits\Mysql;

    public $bill_action_id;
    public $bill_id;
    public $index;
    public $date;
    public $chamber;
    public $note;


    /**
     * @param $bill_id
     * @param $date
     * @param $note
     * @return string
     */
    public static function calculateId($bill_id, $date, $index) {
        return md5($bill_id . $date . $index);
    }

    /**
     * Validates the object
     * @return self
     */
    public function validate() {

        $v = new core\Validator();
        $v->check_text($this->bill_id, 'bill_id', 'Bill Id', 1, 255, true);
        $v->check_number($this->index, 'index', 'Index', true);
        $v->check_date($this->date, 'date', 'Date', true);
        $v->check_text($this->note, 'note', 'Note', 1, pow(2, 32), true);
        $v->done("\n");

        if (!isset($this->bill_action_id))
            $this->bill_action_id = self::calculateId($this->bill_id, $this->date, $this->index);

        return $this;
    }

}