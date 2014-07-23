<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

abstract class BaseAcceptanceTestCase extends PHPUnit_Extensions_Selenium2TestCase {

	protected $captureScreenshotOnFailure = TRUE;
	protected $screenshotPath = '';


	public function setUp()
	{
		$this->screenshotPath = DIR_TESTS_CASES . 'acceptance' . DS . 'screenshots' . DS;
		$this->setBrowser('firefox');
		$this->setBrowserUrl(PATH_WEB);
	}


	public function onNotSuccessfulTest(Exception $e)
	{
		$this->takeScreenshot();
		parent::onNotSuccessfulTest($e);
	}

	protected function takeScreenshot()
	{
		file_put_contents(
			$this->screenshotPath . $this->getName() . '.png',
			$this->currentScreenshot()
		);

	}


} 