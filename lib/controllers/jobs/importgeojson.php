<?php

namespace controllers\jobs;

use \Exception;
use core\CURL;
use core\Debug;
use core\Git;

/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/28/17
 * @package prunejuice
 */
class ImportGeoJson extends \core\Job {
    const REPO_URL  = 'https://github.com/unitedstates/districts';
    const REPO_PATH = '/unitedstates/districts';

    private $states = [
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DC' => 'District of Columbia',
        'DE' => 'Delaware',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'IA' => 'Iowa',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    ];

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

            $year     = floor(date('Y') / 4) * 4;
            $APIPath  = ROOT . '/api/static/us/states';
            $repoPath = ROOT . '/repos/unitedstates/districts';

            foreach ($this->states as $stateAbbr => $stateName) {
                @mkdir("$APIPath/$stateAbbr", 0777, true);
                copy("$repoPath/states/$stateAbbr/shape.geojson", "$APIPath/$stateAbbr/shape.geojson");

                $districts = [
                    0 => $this->getJson("$repoPath/cds/$year/$stateAbbr-0.geojson"),
                ];

                $i = 1;
                do {
                    if ($district = $this->getJson("$repoPath/cds/$year/$stateAbbr-$i/shape.geojson"))
                        $districts[$i] = $district;

                } while ($district && $i++);

                $this->saveToFile("$APIPath/$stateAbbr/districts.geojson", json_encode($districts));
            }

        } catch (Exception $e) {
            Debug::error(__METHOD__ . ': ' . $e->getMessage());
        }
    }

    /**
     * @param $path
     * @return bool|mixed
     */
    function getJson($path) {
        Debug::info("Reading $path");
        if (!file_exists($path))
            return false;
        if (!$data = file_get_contents($path))
            return false;
        if (!$json = json_decode($data))
            Debug::error(json_last_error_msg());

        return $json;
    }

    /**
     * Saves a file, similar to file_put_contents, but creates the directory first.
     * @param $file
     * @param $contents
     * @return mixed
     */
    private function saveToFile($path, $contents) {
        @mkdir(dirname($path), 0777, true);
        if (!file_put_contents($path, $contents))
            return Debug::error("Failed to save file $path") ?? false;

        return true;
    }

}