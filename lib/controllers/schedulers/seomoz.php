<?php

namespace controllers\schedulers;

use core;
use models;

use core\Debug;

/**
 *
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/18/17
 * @package prunejuice
 */
class SEOMoz extends core\Scheduler {

    /** @var string */
    protected static $jobType = 'controllers\\jobs\\SEOMoz';

    /**
     * Schedules the scrape jobs
     */
    public function schedule() {
        $offset  = 0;
        $domains = [];

        // 2 weeks
        $lookback = strtotime('-1 month');

        do {
            $results = models\Domain::findMulti([
                '$or' => [
                    ['moz.dateModified' => ['$eq' => null]],
                    ['moz.dateModified' => ['$lte' => $lookback]],
                ],
            ], ['skip' => $offset]);

            foreach ($results as $domain) {
                $domains[] = $domain->name;
            }

            $offset += 100;

        } while (count($results));

        if (count($domains)) {
            // remove pending domains from the new job array
            $domains = array_diff($domains, $this->getPendingDomains());
            $job     = new models\Job([
                'name'          => self::$jobType,
                'params'        => ['domains' => $domains],
                'status'        => 'queued',
                'scheduledTime' => time(),
                'dateAdded'     => time(),
                'dateModified'  => time(),
            ]);

            $job->save();
        }
    }

    /**
     * Checks the job queue for queued and in_progress domains and returns them as an array
     * @return string[]
     */
    private function getPendingDomains() {
        $jobs = models\Job::findMulti([
            'name' => self::$jobType,
            ['status' => ['$in' => ['queued', 'in_progress']]],
        ]);

        $domains = [];
        foreach ($jobs as $job)
            if (isset($job->params->domains))
                $domains = array_combine($domains, $job->params->domains);

        return array_unique($domains);
    }

}