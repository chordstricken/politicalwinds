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

    const REPO_URL = 'https://github.com/unitedstates/images';
    const REPO_PATH = '/unitedstates/images';

    /**
     * Main work function
     */
    protected function doWork() {
        try {
            $oldHead = Git::head(self::REPO_PATH);
            $result  = Git::pull(self::REPO_URL, self::REPO_PATH);
            $newHead = Git::head(self::REPO_PATH);

            if ($newHead['Hash'] === $oldHead['Hash']) {
                Debug::info(__METHOD__ . " No new updates");
                return;
            }

            $apiPath       = ROOT . '/api/static/members/photos';
            $repoImagePath = ROOT . '/repos/unitedstates/images/congress/450x550';
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