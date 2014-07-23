<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
error_reporting(E_ALL | E_STRICT); // ^ E_WARNING
ini_set('display_errors', 'On');
define('__LOCAL_DEBUG__', true);

define('SYSTEM_PROJECT_NAME', 'ergocrm');


define('PATH_WEB', 'http://ergocrm.local/');
define('DOMAIN_NAME', 'ergocrm.local');
define('SYSTEM_TMP_DIR', '/tmp/');

//DB
define('DB_HOST', 'localhost');
define('DB_NAME', 'ergocrm_tests');
define('DB_USER', 'ergocrm_tests');
define('DB_PASSWORD', 'slider');


require_once dirname(realpath(__DIR__)) . DIRECTORY_SEPARATOR .'config' . DIRECTORY_SEPARATOR . 'config.base.inc.php';

//Session
try {
	if(!Session::isStarted()) {
		session_name(SYSTEM_SESSION_NAME);
		Session::start();
	}
} catch(BaseException $e) {
	/* Someone has started session already? And in a wrong way too! */
}

//Environment specific config
require_once DIR_BASE . "/vendor/autoload.php";

define('DIR_TESTS', DIR_BASE . 'tests' . DS);
define('DIR_TESTS_CASES', DIR_TESTS . 'cases' . DS);
define('DIR_TESTS_SUITES', DIR_TESTS . 'suites' . DS);
define('DIR_TESTS_METADATA', DIR_TESTS . 'testData' . DS);

AutoloaderPool::get('onPHP')->
	addPaths(array(
		DIR_TESTS,
		DIR_TESTS . 'classes' . DS,
		DIR_TESTS_SUITES . DS,
		DIR_TESTS_CASES . 'unit' . DS,
		DIR_TESTS_CASES . 'integration' . DS,
		DIR_TESTS_CASES . 'acceptance' . DS,

		DIR_TESTS_METADATA  . DS
	));


$mainTestDB = DB::spawn('PgSQL', DB_USER, DB_PASSWORD, DB_HOST, DB_NAME);

TestDBPool::me()->
	addLink('main', $mainTestDB)->
	setDefault($mainTestDB);