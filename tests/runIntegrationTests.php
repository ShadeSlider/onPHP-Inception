<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

require_once 'config.tests.inc.php';

$testRunner = new PHPUnit_TextUI_TestRunner();
$allIntegrationTestsSuite = AllIntegrationTestsSuite::suite(SYSTEM_PROJECT_NAME . '-IntegrationTests');

$testRunner->run($allIntegrationTestsSuite);
