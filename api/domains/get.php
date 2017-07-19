<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/17/17
 * @package prunejuice
 */
require_once __DIR__ . '/../../core.php';

use core\api\Response;
use models\Domain;

if (!isset($_REQUEST['name']))
    Response::init('Please provide a name', 400)->send();

if ($result = Domain::findOne(['name' => $_REQUEST['name']]))
    Response::init($result)->send();

Response::init("Domain not found", 404);