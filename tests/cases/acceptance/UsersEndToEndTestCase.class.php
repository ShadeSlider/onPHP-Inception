<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class UsersEndToEndTestCase extends BaseAcceptanceTestCase {

	private $currentUrl;

	public function setUpPage()
	{
		$this->currentUrl = RouterUrlHelper::url(array(SYSTEM_CONTROLLER_VAR_NAME => 'backend-user'), 'entity');
	}


	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 **************************************************************/

}