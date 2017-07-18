<?php
require __DIR__ . '/../core.php';

$dParam = core\Format::domain($argv[1]);

if (!models\Domain::findOne(['name' => $dParam]))
    models\Domain::new([
        'name'         => $dParam,
        'dateAdded'    => time(),
        'dateModified' => time(),
    ])->save();

die(0);