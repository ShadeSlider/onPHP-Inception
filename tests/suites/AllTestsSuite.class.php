<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class AllTestsSuite extends PHPUnit_Framework_TestSuite {

	public static function suite()
	{
		$suite = new static;
		$suite->addTestSuite(AllUnitTestsSuite::suite());
		$suite->addTestSuite(AllIntegrationTestsSuite::suite());
		$suite->addTestSuite(AllAcceptanceTestsSuite::suite());

		return $suite;
	}

	public function run(PHPUnit_Framework_TestResult $result = null)
	{
		return parent::run($result);
	}
}