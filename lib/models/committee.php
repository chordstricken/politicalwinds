<?php

namespace models;

use core;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/26/17
 * @package politics
 *
type: senate
name: Senate Committee on the Judiciary
url: http://judiciary.senate.gov/
thomas_id: SSJU
senate_committee_id: SSJU
subcommittees:
 */
class Committee extends core\Model {
    use core\db\Filesystem;

    public $id;
    public $type;
    public $name;
    public $url;
    public $subcommittees;
    public $members;

    /** @return string */
    protected function getPath() {
        return ROOT . "/api/static/us/committees/$this->id.json";
    }

    /**
     * Validates the object
     */
    public function validate() {
        $v = new core\Validator();
        $v->check_text($this->id, 'id', 'ID', 4, 7, true);
        $v->done("\n");
    }

}