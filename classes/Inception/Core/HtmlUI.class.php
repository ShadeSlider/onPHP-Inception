<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class HtmlUI {

	protected $controller = null;

	public function __construct(BaseController $controller = null)
	{
		$this->controller = $controller;
	}


	/**
	 * @return HtmlUI
	 */
	public static function create($controller = null)
	{
		return new static($controller);
	}


	/**
	 * @return string
	 */
	public function css($fileName)
	{
		return PATH_WEB_CSS . $fileName;
	}


	/**
	 * @return string
	 */
	public function js($fileName)
	{
		return PATH_WEB_JS . $fileName;
	}


	/**
	 * @return string
	 */
	public function file($fileName)
	{
		return PATH_WEB . $fileName;
	}


	/**
	 * @return string
	 */
	public function img($fileName)
	{
		return PATH_WEB_IMG . $fileName;
	}

	public function url($route, $params = array(), $reset = true, $encode = false)
	{
		if($this->controller && !isset($params['controller'])) {
			$params['controller'] = $this->controller->getCleanDashedName();
		}

		return RouterUrlHelper::url($params, $route, $reset, $encode);
	}

	public function controllerUrl($controller, $params = array(), $reset = true, $encode = false)
	{
		$route = 'common';
		$params['controller'] = $controller;
		return $this->url($route, $params, $route, $reset, $encode);
	}

	public function controllerActionUrl($controller, $action, $params = array(), $reset = true, $encode = false)
	{
		$route = 'common';
		$params['controller'] = $controller;
		$params['action'] = $action;

		return $this->url($route, $params, $route, $reset, $encode);
	}


	public function isAuthenticatedUser()
	{
		return BackendAccessManager::me()->isUserAuthenticated();
	}


	public function canAccess($controller, $action = null)
	{
		if($action) {
			return BackendAccessManager::me()->hasAccessToControllerAction($controller, $action);
		}
		else {
			return BackendAccessManager::me()->hasFullAccessToController($controller);
		}
	}

	public function canAccessAny($controllers, $actions = array())
	{
		if(count($actions)) {
			Assert::isEqual(count($controllers), count($actions));
		}

		foreach ($controllers as $idx => $controller) {
			if(count($actions)) {
				$canAccess = $this->canAccess($controller, $actions[$idx]);
			}
			else {
				$canAccess = $this->canAccess($controller);
			}

			if($canAccess) {
				return true;
			}
		}

		return false;
	}

	public function canAccessAll($controllers, $actions = array())
	{
		if(count($actions)) {
			Assert::isEqual(count($controllers), count($actions));
		}

		foreach ($controllers as $idx => $controller) {
			if(count($actions)) {
				$canAccess = $this->canAccess($controller, $actions[$idx]);
			}
			else {
				$canAccess = $this->canAccess($controller);
			}

			if(!$canAccess) {
				return false;
			}
		}

		return true;
	}
}