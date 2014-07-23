<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class RoutingEndToEndTestCase extends BaseAcceptanceTestCase {
	public function prepareSession()
	{
		$session = parent::prepareSession();

		$this->url(HtmlUI::create()->controllerUrl('login'));


		$mainForm = $this->byId('mainForm');
		$mainForm->byId('inputLogin')->value('admin');
		$mainForm->byId('inputPassword')->value('slider');
		$submitButton = $mainForm->byCssSelector('button[type=submit]');
		$submitButton->click();


		return $session;
	}



	/**
	 * @test
	 */
	public function openUrl_indexRoute_htmlContainsProperWrapperTag()
	{
		$this->url(PATH_WEB);

		$wrapperDiv = array(
			'id' => 'wrapper',
			'attributes' => array(
				'class' => 'index index-default'
			)
		);
		$this->assertTag($wrapperDiv, $this->source(), 'Required TAG has not been found.');
	}


	/**
	 * @test
	 */
	public function openUrl_nonexistentRoute_htmlContainsProperWrapperTag()
	{
		$this->url(PATH_WEB . 'nonexistent');

		$wrapperDiv = array(
			'id' => 'wrapper',
			'attributes' => array(
				'class' => 'not-found not-found-default'
			)
		);

		$this->assertTag($wrapperDiv, $this->source(), 'Required TAG has not been found.');
	}

	/**
	 * @test
	 */
	public function openUrl_internalErrorRoute_htmlContainsProperWrapperTag()
	{
		$htmlUI = HtmlUI::create();
		$this->url($htmlUI->url('internal-error'));

		$wrapperDiv = array(
			'id' => 'wrapper',
			'attributes' => array(
				'class' => 'internal-error internal-error-default'
			)
		);

		sleep(1); //Fixes a supposed bug in Selenium, when page isn't fully loaded.
		$this->assertTag($wrapperDiv, $this->source(), 'Required TAG has not been found.');
	}
}