<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

require_once 'config.tests.inc.php';

$testRunner = new PHPUnit_TextUI_TestRunner();
$allAcceptanceTestsSuite = AllAcceptanceTestsSuite::suite(SYSTEM_PROJECT_NAME . '-AcceptanceTests');

$testRunner->run($allAcceptanceTestsSuite);
