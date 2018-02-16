<?php

date_default_timezone_set('America/Los_Angeles');

$config = json_decode(file_get_contents('./config.json'));

var_dump($config);
