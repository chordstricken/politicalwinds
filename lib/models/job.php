<?php
namespace models;

use core;
use \Exception;

/**
 * @author Jason Wright <jason@silvermast.io>
 * @since 7/12/17
 */
class Job extends core\Model {
    use core\db\traits\Blackhole;

    public $id;
    public $name;
    public $params;
    public $startTime;
    public $endTime;
    public $scheduledTime;
    public $elapsed;
    public $status;
    public $dateAdded;

    /**
     * @return $this
     * @throws Exception
     */
    public function validate() {
        if (mb_strlen($this->id) > 1024) throw new Exception('Invalid id', 400);
        if (mb_strlen($this->name) > 1024) throw new Exception('Invalid name', 400);

        return $this;
    }

}