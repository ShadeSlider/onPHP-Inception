<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class BaseTestSuite extends PHPUnit_Framework_TestSuite {
	const TEST_DIR_SUFFIX = '';
	protected $testDir;

	public function __construct($theClass = '', $name = '')
	{
		$this->testDir = DIR_TESTS_CASES;

		if(static::TEST_DIR_SUFFIX) {
			$this->testDir .= static::TEST_DIR_SUFFIX . DS;
		}

		parent::__construct($theClass, $name);
	}

	public function run(PHPUnit_Framework_TestResult $result = null)
	{
		return parent::run($result);
	}

	public static function suite($name = '')
	{
		$suite = new static('', $name);

		foreach (glob($suite->testDir . '*TestCase*.php') as $testFile) {
			$suite->addTestFile($testFile);
		}
		return $suite;
	}
}