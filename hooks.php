<?php

require('vendor/autoload.php');

$handler = new GithubWebHook\Handler();

if($handler->handle($_POST)) {
    echo "OK";
} else {
    echo "Wrong request";
}


