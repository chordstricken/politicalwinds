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
    public $committees;

    public function __set($field, $value) {
        switch ($field) {
            case 'name':
                $value  = (object)$value;
                $aValue = (array)$value;

                if (!isset($aValue['official_full']))
                    $value->official_full = implode(' ', $aValue);

                return $this->$field = $value;

            default:
                return $this->$field = $value;
        }
    }

    /** @return string */
    protected function getPath() {
        $id = (string)$this->id;
        return ROOT . "/api/static/members/{$id[0]}/$id.json";
    }

    /**
     * Validates the object
     * @return self
     */
    public function validate() {
        $v = new core\Validator();

        $v->check_text($this->id, 'id', 'ID', 4, 16, true);

        $v->done("\n");
        return $this;
    }

    /**
     * Formats & standardizes the object
     * @return self
     */
    public function format() {

        $this->id = (string)$this->id;

        return $this;
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

    public function isInOffice() {
        $now = date('Ymd');
        $term = $this->getCurrentTerm();
        $start = str_replace('-', '', $term->start);
        $end   = str_replace('-', '', $term->end);
        return $now >= $start && $now <= $end;
    }

    /**
     * Generates an index entry intended for congress.json
     * @return array
     */
    public function getIndexEntry() {
        return [
            'id' => $this->id,
            'name' => $this->name->official_full ?? implode(' ', (array)$this->name),
            'party' => $this->getCurrentTerm()->party,
            'office' => $this->getOffice(),
            'state' => $this->getCurrentTerm()->state ?? null,
            'district' => $this->getCurrentTerm()->district ?? null,
        ];
    }

}