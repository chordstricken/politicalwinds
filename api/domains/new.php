<?php
/**
 * @author Jason Wright <jason.dee.wright@gmail.com>
 * @since 7/18/17
 * @package prunejuice
 */
require_once __DIR__ . '/../../core.php';

use core\api\Response;
use models\Domain;

if (empty($_REQUEST['name']))
    Response::init('Please provide a name', 400)->send();

if ($result = Domain::findOne(['name' => $_REQUEST['name']]))
    Response::init("$result->name already exists.", 400)->send();


$result = Domain::new([
    'name'         => core\Format::domain($_REQUEST['name']),
    'dateModified' => time(),
    'dateAdded'    => time(),
])->validate()->save();

Response::init($result)->send();