<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
$currentDir = dirname(realpath(__FILE__));
require_once $currentDir . DIRECTORY_SEPARATOR . '../../config/config.inc.php';

$dbManager = DBManager::create(DBPool::me());

if(!defined('__LOCAL_DEBUG__')) {
	exit('Are u mad? We are not in DEV environment!');
}

try {
	$dbManager->dropDBTables();
}
catch (BaseException $e) {
	print_r($e->getMessage());
	print_r($e->getTraceAsString());
}