<?php
namespace models;

use core;
use \Exception;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 7/12/17
 */
class Archive extends core\Model {

    const TABLE = 'archive';

    public $id;
    public $url;
    public $headers;
    public $contents;

    /**
     * @return $this
     * @throws Exception
     */
    public function validate() {
        if (mb_strlen($this->id) > 1024) throw new Exception('Invalid id', 400);
        if (mb_strlen($this->url) > 1024) throw new Exception('Invalid url', 400);

        return $this;
    }

}