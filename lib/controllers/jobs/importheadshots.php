<?php

namespace controllers\jobs;

use \Exception;
use core\CURL;
use core\Debug;
use core\Git;
use models\Member;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package prunejuice
 */
class ImportHeadshots extends \core\Job {

    /**
     * Main work function
     */
    protected function doWork() {
        try {
            $result = Git::pull('https://github.com/unitedstates/images', '/unitedstates/images');
            Debug::info($result);

            $apiPath       = ROOT . '/api/static/members/photos';
            $repoImagePath = ROOT . '/repos/unitedstates/images/congress/225x275';
            $repoImages    = scandir($repoImagePath);

            foreach ($repoImages as $image) {
                if ($image[0] === '.') continue;

                $dest = "$apiPath/$image[0]/$image";
                if (!is_dir(dirname($dest)))
                    mkdir(dirname($dest), 0777, true);

                copy("$repoImagePath/$image", $dest);
            }


        } catch (Exception $e) {
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
        }
    }

}