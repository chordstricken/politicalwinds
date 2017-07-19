<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/17/17
 * @package prunejuice
 */
require_once __DIR__ . '/../../core.php';

use core\api\Response;
use models\Job;

if (!isset($_REQUEST['id']))
    Response::init('Please provide a id', 400)->send();

if ($result = Job::findOne(['id' => $_REQUEST['id']]))
    Response::init($result)->send();

Response::init("Job not found", 404);