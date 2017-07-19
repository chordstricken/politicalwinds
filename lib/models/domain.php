<?php
namespace models;

use core;
use \Exception;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 7/11/17
 */
class Domain extends core\Model {

    const TABLE = 'domains';

    public $id;
    public $name;
    public $resolvedUrl;

    /** @var \stdClass */
    public $scrape;

    /** @var \stdClass */
    public $moz;

    /** @var \stdClass */
    public $sitemap;


    public $dateModified;
    public $dateAdded;

    /**
     * @return $this
     * @throws Exception
     */
    public function validate() {
        if (mb_strlen($this->id) > 1024) throw new Exception('Invalid id', 400);
        if (mb_strlen($this->name) > 1024 || !mb_strlen($this->name)) throw new Exception('Invalid name', 400);

        return $this;
    }

}