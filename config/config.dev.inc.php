<?php
error_reporting(E_ALL | E_STRICT); // ^ E_WARNING
ini_set('display_errors', 'On');

define('__LOCAL_DEBUG__', true);
require_once DIR_BASE . "/vendor/autoload.php";