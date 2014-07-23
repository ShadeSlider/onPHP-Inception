<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014
 */

class HtmlUITestCase extends PHPUnit_Framework_TestCase {


	/**
	 * @test
	 */
	public function css_withString_returnCSSFileURL()
	{

		$htmlUI = $this->makeHtmlUI();
		$this->assertEquals(
			$htmlUI->css('inception.css'),
			PATH_WEB_CSS . 'inception.css'
		);
	}


	/**
	 * @test
	 */
	public function js_withString_returnJavaScriptFileURL()
	{

		$htmlUI = $this->makeHtmlUI();
		$this->assertEquals(
			$htmlUI->js('inception.js'),
			PATH_WEB_JS . 'inception.js'
		);
	}


	/**
	 * @test
	 */
	public function img_withString_returnImgFileURL()
	{

		$htmlUI = $this->makeHtmlUI();
		$this->assertEquals(
			$htmlUI->img('inception.png'),
			PATH_WEB_IMG . 'inception.png'
		);
	}


	/**
	 * @test
	 */
	public function file_withString_returnFileURLUsingWebRoot()
	{

		$htmlUI = $this->makeHtmlUI();
		$this->assertEquals(
			$htmlUI->file('inception.txt'),
			PATH_WEB . 'inception.txt'
		);
	}

	/**************************************************************
	 **************** TEST CREATION METHODS & UTILS ***************
	 **************************************************************/

	/**
	 * @return HtmlUI
	 */
	protected function makeHtmlUI()
	{
		$htmlUI = HtmlUI::create();
		return $htmlUI;
	}

}

/**************************************************************
 ******************** FAKE CLASSES FOR TESTS ******************
 **************************************************************/

