<?php
ini_set("memory_limit", "-1");

include 'vendor/autoload.php';

(new \Crowbar\Application())->setup()->run();