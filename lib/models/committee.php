<?php

namespace models;

use core;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/26/17
 * @package politics
 */
class Committee extends core\Model {
    const TABLE = 'committee';
    const INDEX = 'committee_id';

    use core\db\traits\Mysql;

    public $committee_id;
    public $parent;
    public $thomas_id;
    public $type;
    public $name;
    public $url;
    public $minority_url;
    public $address;
    public $phone;

    /**
     * Validates the object
     */
    public function validate() {
        $v = new core\Validator();
        $v->done("\n");
    }

}