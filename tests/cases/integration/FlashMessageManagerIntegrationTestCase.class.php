<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class FlashMessageManagerIntegrationTestCase extends BaseIntegrationTestCase {

	const TEST_MESSAGE_NAME = 'testMessage';
	const TEST_MESSAGE_TEXT = 'Test Message';


	/**
	 * @test
	 */
	public function addMessage_validData_setsFlashMessageToSession()
	{
		$flashMessageManager = $this->makeFlashMessageManager();

		$flashMessageManager->addMessage(self::TEST_MESSAGE_NAME, self::TEST_MESSAGE_TEXT);

		$this->assertEquals($_SESSION[FlashMessageManager::SESSION_VAR_NAME][self::TEST_MESSAGE_NAME], self::TEST_MESSAGE_TEXT);
	}


	/**
	 * @test
	 */
	public function addMessages_validData_setsFlashMessagesToSession()
	{
		$flashMessageManager = $this->makeFlashMessageManager();

		$flashMessageManager->addMessages(
			array(
				self::TEST_MESSAGE_NAME => self::TEST_MESSAGE_TEXT,
				self::TEST_MESSAGE_NAME . '2' => self::TEST_MESSAGE_TEXT . '2'
			)
		);

		$this->assertEquals($_SESSION[FlashMessageManager::SESSION_VAR_NAME][self::TEST_MESSAGE_NAME], self::TEST_MESSAGE_TEXT);
		$this->assertEquals($_SESSION[FlashMessageManager::SESSION_VAR_NAME][self::TEST_MESSAGE_NAME . '2'], self::TEST_MESSAGE_TEXT . '2');
	}


	/**
	 * @test
	 */
	public function getMessage__invalidMessageName_returnsNull()
	{
		$flashMessageManager = $this->makeFlashMessageManager();

		$message = $flashMessageManager->getMessage('iDontExist');

		$this->assertNull($message);
	}


	/**
	 * @test
	 */
	public function getMessage__validMessageName_returnsFlashMessageTextAndUnsetsInSession()
	{
		$flashMessageManager = $this->makeFlashMessageManager();

		$flashMessageManager->addMessage(self::TEST_MESSAGE_NAME, self::TEST_MESSAGE_TEXT);

		$message = $flashMessageManager->getMessage(self::TEST_MESSAGE_NAME);

		$this->assertNotEmpty($message);
		$this->assertTrue(empty($_SESSION[FlashMessageManager::SESSION_VAR_NAME][self::TEST_MESSAGE_NAME]));
	}


	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 **************************************************************/
	protected function makeFlashMessageManager()
	{
		$flashMessageManager = FlashMessageManager::create();

		return $flashMessageManager;
	}
}

/**************************************************************
 ******************** FAKE CLASSES FOR TESTS ******************
 **************************************************************/

