<?php

namespace core;

use models;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/12/17
 * @package prunejuice
 */
abstract class Job {

    /** @var models\Job */
    public $job;

    /** @var object|\stdClass */
    public $params;

    /**
     * Job constructor.
     * @param mixed $params
     */
    public function __construct($params = null) {
        set_time_limit(-1);

        $this->params = !$params ? new \stdClass() : (object)$params;

        $this->job = new models\Job([
            'name'      => get_class($this),
            'params'    => $this->params,
            'startTime' => time(),
            'status'    => 'in_progress',
            'dateAdded' => time(),
        ]);
    }

    /**
     * Runs the job
     */
    public function run() {
        Debug::info("Starting Job {$this->job->name}");

        try {
            $this->doWork();
            $this->job->status = 'complete';

        } catch (\Exception $e) {
            Debug::error($e);
            $this->job->status = 'failed';
            $this->job->error  = $e->getMessage();
        }

        $this->job->endTime = time();
        $this->job->elapsed = $this->job->endTime - $this->job->startTime;
        $this->job->save();

        Debug::info("Finished Job {$this->job->name}");
    }

    protected abstract function doWork();

}