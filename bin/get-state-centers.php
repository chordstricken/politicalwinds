<?php
require_once __DIR__ . '/../core.php';
ini_set('display_errors', 1);

$centers = [
    'AL' => null,
    'AK' => null,
    'AZ' => null,
    'AR' => null,
    'CA' => null,
    'CO' => null,
    'CT' => null,
    'DC' => null,
    'DE' => null,
    'FL' => null,
    'GA' => null,
    'HI' => null,
    'IA' => null,
    'ID' => null,
    'IL' => null,
    'IN' => null,
    'KS' => null,
    'KY' => null,
    'LA' => null,
    'ME' => null,
    'MD' => null,
    'MA' => null,
    'MI' => null,
    'MN' => null,
    'MS' => null,
    'MO' => null,
    'MT' => null,
    'NE' => null,
    'NV' => null,
    'NH' => null,
    'NJ' => null,
    'NM' => null,
    'NY' => null,
    'NC' => null,
    'ND' => null,
    'OH' => null,
    'OK' => null,
    'OR' => null,
    'PA' => null,
    'RI' => null,
    'SC' => null,
    'SD' => null,
    'TN' => null,
    'TX' => null,
    'UT' => null,
    'VT' => null,
    'VA' => null,
    'WA' => null,
    'WV' => null,
    'WI' => null,
    'WY' => null,
];

function average($arr) {
    return array_sum($arr) / count($arr);
}

foreach ($centers as $state => &$center) {
    $lat = [];
    $lon = [];

    $data = json_decode(file_get_contents(ROOT . "/api/static/us/states/$state/shape.geojson"), true);
    foreach ($data['coordinates'] as $t1) {
        foreach ($t1 as $t2) {
            foreach ($t2 as $t3) {
                $lat[] = doubleval($t3[0]);
                $lon[] = doubleval($t3[1]);
            }
        }
    }

    $center = [average($lat), average($lon)];
}

echo json_encode($centers, JSON_PRETTY_PRINT) . "\n";