<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class BaseDBReliantTestSuite extends BaseTestSuite {

	public function run(PHPUnit_Framework_TestResult $result = null)
	{
		$dbTestManager = TestDBManager::create();
		$dbTestManager->
			dropAndCreateDBTables()->
			fillDB()
		;

		parent::run($result);
	}
}