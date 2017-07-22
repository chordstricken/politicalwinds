<?php

namespace controllers\jobs;

use \Exception;
use core\CURL;
use core\Debug;
use models\Member;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/20/17
 * @package prunejuice
 */
class ImportHeadshots extends \core\Job {

    private $total = 0;
    private $i     = 0;

    /**
     * Main work function
     */
    protected function doWork() {
        try {

            if (!$index = file_get_contents(ROOT . '/api/static/us/current.json'))
                throw new Exception('us/current.json not found');

            if (!$index = json_decode($index))
                throw new Exception('Failed to decode JSON.' . json_last_error_msg());

            $this->total = count(get_object_vars($index));

            foreach ($index as $bioguide => $mData)
                $this->acquireMemberHeadshot($bioguide);

        } catch (Exception $e) {
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    private function acquireMemberHeadshot($id) {
        $this->i++;

        try {
            $imgPath = "$id[0]/$id.jpg";
            $imgFile = ROOT . "/api/static/members/photos/$imgPath";
            $imgDir  = dirname($imgFile);

            if (file_exists($imgFile)) {
                Debug::info("Image already stored. $id ($this->i / $this->total)");
                return;
            }

            $curl    = CURL::new("http://bioguide.congress.gov/bioguide/photo/$imgPath");
            $img     = $curl->exec();

            if ($curl->code() < 400) {

                @mkdir($imgDir, 0777, true);

                if (!file_put_contents($imgFile, $img))
                    throw new Exception("Failed to save file $imgFile");

                Debug::info("Acquired Headshot $id ($this->i / $this->total)");

            } else {
                Debug::info("Failed to get Headshot $id ($this->i / $this->total)");

            }

        } catch (Exception $e) {
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
        }

    }

}