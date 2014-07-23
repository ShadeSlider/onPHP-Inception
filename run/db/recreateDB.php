<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

$currentDir = dirname(realpath(__FILE__));
require_once $currentDir . DIRECTORY_SEPARATOR . '../../config/config.inc.php';
require_once PATH_CLASSES . 'Auto' . DS . 'schema.php';

define('INITIAL_DATA_DIR', $currentDir . DIRECTORY_SEPARATOR . 'initialData' . DIRECTORY_SEPARATOR);

$dbManager = DBManager::create(DBPool::me(), $schema);

try {
	$dbManager->dropAndCreateDBTables();
}
catch (BaseException $e) {
	print_r($e->getMessage());
	print_r($e->getTraceAsString());
}

include_once(INITIAL_DATA_DIR . 'allInitialData.php');