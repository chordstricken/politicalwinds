<?php
require_once __DIR__ . '/../../core.php';

$query = isset($_REQUEST['query']) ? json_decode($_REQUEST['query']) : [];

$results = models\Domain::findMulti($query, ['sort' => ['moz.domainAuthority' => -1], 'limit' => 50]);

core\api\Response::init($results)->send();