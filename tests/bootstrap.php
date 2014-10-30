<?php

define('FIXTURES_DIR', __DIR__.'/fixtures');

$autoloader = require __DIR__.'/../vendor/autoload.php';
$autoloader->add('deit\\compression', [ __DIR__.'/../src', __DIR__.'/../tests' ]);
