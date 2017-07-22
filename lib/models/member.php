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
    use core\db\Filesystem;

    public $id;
    public $appIds;
    public $name;
    public $terms;
    public $social;
    public $bio;

    /** @return string */
    protected function getPath() {
        return ROOT . "/api/static/members/{$this->id[0]}/$this->id.json";
    }

    /**
     * Validates the object
     */
    public function validate() {
        $v = new core\Validator();

        $v->check_text($this->id, 'id', 'ID', 7, 7, true);

        $v->done("\n");
    }

    /** @return object */
    public function getCurrentTerm() {
        return end($this->terms);
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

    /**
     * Generates an index entry intended for congress.json
     * @return array
     */
    public function getIndexEntry() {
        return [
            'id' => $this->id,
            'name' => $this->name->official_full,
            'party' => $this->getCurrentTerm()->party,
            'office' => $this->getOffice(),
            'state' => $this->getCurrentTerm()->state,
            'district' => $this->getCurrentTerm()->district,
        ];
    }

}