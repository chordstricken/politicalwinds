<?php

namespace models;

use core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 8/17/17
 * @package politics
 */
class Bill extends core\Model {
    const TABLE = 'bill';
    const INDEX = 'bill_id';

    use core\db\traits\Mysql;

    public $bill_id;
    public $code;
    public $amends_bill;
    public $title;
    public $title_senate;
    public $title_house;
    public $summary;
    public $session;
    public $link;
    public $sponsor;
    public $document_full;

    /**
     * Generates a unique ID for the bill
     * @param $code
     * @param $session
     * @return string
     */
    public static function calculateId($code, $session) {
        $code   = strtolower(preg_replace('@[^\w\d.]@mis', '', $code));
        $session = intval($session);
        return "$session-$code";
    }

    /**
     * Validates the object
     * @return self
     */
    public function validate() {

        if (!isset($this->bill_id))
            $this->bill_id = self::calculateId($this->code, $this->session);

        return $this;
    }

}