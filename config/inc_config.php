<?php

$GLOBALS['config']['db'] = array(
    'host'  => 'localhost',
    'port'  => 3306,
    'user'  => 'root',
    'pass'  => 'root',
    'name'  => 'phpspider',
);

$GLOBALS['config']['redis'] = array(
    'host'      => '127.0.0.1',
    'port'      => 6379,
    'pass'      => '',
    'prefix'    => 'phpspider',
    'timeout'   => 30,
);

include "inc_mimetype.php";
