<?php

namespace controllers\jobs;

use \Exception;
use core\CURL;
use core\Debug;
use models\Domain;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/12/17
 * @package prunejuice
 */
class Sitemap extends \core\Job {

    /** @var Domain[] */
    private $domains;

    /**
     * Main work function
     */
    protected function doWork() {

        if (!isset($this->params->domains) || !is_array($this->params->domains))
            throw new Exception("No Domains to pull.");

        $this->params->domains = array_map("core\\Format::domain", $this->params->domains);

        $this->domains = Domain::findMulti(['name' => ['$in' => array_values($this->params->domains)]]);

        foreach ($this->domains as $domain) {

            Debug::info(__METHOD__ . " $domain->name");

            $this->parseSitemaps($domain);

            try {
                $domain->sitemap->dateModified = time();
                $domain->update([
                    'sitemap'      => $domain->sitemap,
                    'dateModified' => $domain->dateModified,
                ]);
            } catch (Exception $e) {
                Debug::error($e);
            }

            Debug::info("Finished Scrape $domain->name");
        }
    }

    /**
     * Pulls down the sitemap and checks for a last edited date
     * @param Domain $domain
     */
    private function parseSitemaps(Domain $domain) {
        Debug::info(__METHOD__ . " $domain->name");

        $curl = CURL::new("http://$domain->name/sitemap.xml");
        $xml  = $curl->exec();

        if ($curl->code() > 400)
            return;

        $xml = simplexml_load_string($xml);

        $domain->sitemap->lastmod = 0;

        // check for URL nodes
        foreach ($xml as $wrapper) {

            if (isset($wrapper->loc))
                $wrapper = [$wrapper];

            foreach ($wrapper as $group) {
                // because we're trying to assert when the last mod date was,
                // see if we can grab the most recent of any page in general.
                if ($group->lastmod) {
                    $lastmodTime = strtotime($group->lastmod);
                    if ($lastmodTime > $domain->sitemap->lastmod) {
                        $domain->sitemap->lastmod = $lastmodTime;
                        $domain->sitemap->loc     = $group->loc;
                    }
                }

            }
        }
    }

}