<?php

/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class ExtendedDebugPhpView extends ExtendedPhpView {

	const DOCTYPE_PATTERN = '/(<!DOCTYPE[\w\d\s>\pP]+)/';

	/** @var  Model */
	protected $model;

	/**
	 * @return SimplePhpView
	 **/
	public function render(/* Model */ $model = null)
	{
		$this->model = $model;

		ob_start();
		parent::render($model);
		$content = ob_get_contents();
		ob_end_clean();

		$doctypeMatches = array();
		preg_match(self::DOCTYPE_PATTERN, $content, $doctypeMatches);

		if(count($doctypeMatches) > 0 && !empty($doctypeMatches[0])) {
			$content = preg_replace(self::DOCTYPE_PATTERN, '', $content);
			$content = $doctypeMatches[0] . $content;
		}

		echo $content;

		return $this;
	}


	protected function preRender()
	{
		if(defined('SYSTEM_DEBUG_PRINTED')) {
			echo "\n<!-- Template: " . $this->templatePath . "-->\n";
			return;
		}

		$request = HttpRequest::createFromGlobals();
		if($this->model instanceof Model) {
			extract($this->model->getList());
		}

		$url = Utils::getCurrentURL();

		$debugStr = "<!--\n";

		$debugStr .= "URL: " . $url . "\n";

		if(isset($controllerName) && is_string($controllerName)) {
			$debugStr .= "Controller: " . $controllerName . "\n";
		}

		if(isset($controllerActionName) && is_string($controllerActionName)) {
			$debugStr .= "Action: " . $controllerActionName . "\n";
		}
		elseif(isset($action) && is_string($action)) {
			$debugStr .= "Action: " . $action . "\n";
		}

		$debugStr .= "Layout: " . $this->templatePath . "\n";
		$debugStr .= "Request Method: " . $request->getMethod()->getName() . "\n";

		//GET
		if(count($request->getGet()) > 0) {
			$debugStr .= "\n=======================================================\n\n";
			$debugStr .= 'GET: ' . print_r($request->getGet(), true);
			$debugStr .= "\n=======================================================\n\n";

		}

		//POST
		if(count($request->getPost()) > 0) {
			$debugStr .= "\n=======================================================\n\n";
			$debugStr .= 'POST: ' . print_r($request->getPost(), true);
			$debugStr .= "\n=======================================================\n\n";
		}

		//SESSION
		if(count($request->getSession()) > 0) {
			$debugStr .= "\n=======================================================\n\n";
			$debugStr .= 'SESSION: ' . print_r($request->getSession(), true);
			$debugStr .= "\n=======================================================\n\n";
		}

		//COOKIE
		if(count($request->getCookie()) > 0) {
			$debugStr .= "\n=======================================================\n\n";
			$debugStr .= 'COOKIE: ' . print_r($request->getCookie(), true);
			$debugStr .= "\n=======================================================\n\n";
		}


		$debugStr .= "-->\n";

		$request = HttpRequest::createFromGlobals();
		if(!AjaxUtils::me()->isAjaxRequest($request)) {
			echo $debugStr;

		}
		define('SYSTEM_DEBUG_PRINTED', true);
	}
}