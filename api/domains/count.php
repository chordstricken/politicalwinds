<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/17/17
 * @package prunejuice
 */
require_once __DIR__ . '/../../core.php';

$query = isset($_REQUEST['query']) ? json_decode($_REQUEST['query']) : [];

$results = models\Domain::count($query);

core\api\Response::init($results)->send();