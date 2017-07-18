<?php
require_once __DIR__ . '/../../core.php';

$query = isset($_REQUEST['query']) ? json_decode($_REQUEST['query']) : [];

$domains = models\Job::findMulti($query, ['sort' => ['startTime' => -1], 'limit' => 50]);

core\api\Response::init($domains)->send();