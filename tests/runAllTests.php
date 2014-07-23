<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

require_once 'config.tests.inc.php';

//$testRunner = new PHPUnit_TextUI_TestRunner();
//$allUnitTestsSuite = new AllUnitTestsSuite(SYSTEM_PROJECT_NAME . '-UnitTests');
//$allIntegrationTestsSuite = new AllIntegrationTestsSuite(SYSTEM_PROJECT_NAME . '-IntegrationTests');
//$allAcceptanceTestsSuite = new AllAcceptanceTestsSuite(SYSTEM_PROJECT_NAME . '-AcceptanceTests');

printTestSuiteHeader('Unit');
require_once('runUnitTests.php');
//$testRunner->run($allUnitTestsSuite);

printTestSuiteHeader('Integration');
require_once('runIntegrationTests.php');
//$testRunner->run($allIntegrationTestsSuite);

printTestSuiteHeader('Acceptance');
require_once('runAcceptanceTests.php');
//$testRunner->run($allAcceptanceTestsSuite);


function printTestSuiteHeader($testType)
{
	echo "\n\n===============================================\n";
	echo "Running " . $testType . " Tests:\n";
	echo "===============================================\n\n";
}