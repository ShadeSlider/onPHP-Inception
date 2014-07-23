<?php
/**
  * User: Eric Gorbikov
 * Date: 4/15/14
 * Time: 3:06 AM
  */

class BackendAccessManager extends Singleton {

	const SESSION_AUTHENTICATED_USER_VAR = 'SESSION_AUTHENTICATED_USER_ID';
	const COOKIE_AUTHENTICATED_USER_VAR_PREFIX = 'USER_';
	const COOKIE_AUTHENTICATED_USER_COOKIE_MAX_AGE_IN_DAYS = 7;
	const ADMIN_ROLE_NAME = 'admin';

	/** @var BackendUser */
	private $authenticatedUser;
	
	/** @var  BackendAccessResource[] */
	private $accessibleResourcesByRole = array();
	private $accessibleResourcesHashMap = array();

	/**
	 * @return BackendAccessManager
	 */
	public static function me()
	{
		return self::getInstance(__CLASS__);
	}


	/**
	 * @return BackendUser|null
	 */
	public function autoAuthenticate()
	{
		if($this->isUserAuthenticated()) {
			return $this->getAuthenticatedUser();
		}

		return null;
	}


	/**
	 * @return bool
	 */
	public function isUserAuthenticated()
	{
		if($this->authenticatedUser) {
			return true;
		}

		$user = null;
		while(true) {
			/** Looking in Session */
			$userIdFromSession = (int)Session::get(static::SESSION_AUTHENTICATED_USER_VAR);
			if(is_int($userIdFromSession) && $userIdFromSession > 0) {
				$user = BackendUser::dao()->getById($userIdFromSession);
				if($user) {
					break;
				}
			}


			/** Looking in Cookie */
			if(isset($_COOKIE[$this->getUserCookieVarName()])) {
				$userCookie = $_COOKIE[$this->getUserCookieVarName()];
				if($userCookie) {
					$user = BackendUser::dao()->getByMD5HashedId($userCookie);
				}
				if($user) {
					Session::assign(static::SESSION_AUTHENTICATED_USER_VAR, $user->getId());
					break;
				}
			}


			break;
		}

		if(!$user instanceof BackendUser || !$user->getId()) {
			return false;
		}


		$this->setUpUser($user);

		return true;
	}


	/**
	 * @return BackendUser|null
	 */
	public function authenticate($userLoginOrEmail, $userPassword, $setCookie = false)
	{
		$user = BackendUser::dao()->getByLoginAndPassword($userLoginOrEmail, $userPassword);

		if(!$user) {
			$user = BackendUser::dao()->getByEmailAndPassword($userLoginOrEmail, $userPassword);

			if(!$user) {
				return null;
			}
		}

		Session::assign(static::SESSION_AUTHENTICATED_USER_VAR, $user->getId());

		if($setCookie) {
			$this->setUserCookie(md5($user->getId()));
		}

		$this->setUpUser($user);
		return $user;
	}


	/**
	 * @return static
	 */
	public function logout()
	{
		Session::drop(static::SESSION_AUTHENTICATED_USER_VAR);
		if(isset($_COOKIE[$this->getUserCookieVarName()])) {
			$this->dropUserCookie();
		}

		$this->authenticatedUser = null;
		$this->accessibleResourcesByRole = array();
		$this->accessibleResourcesHashMap = array();

		return $this;
	}


	/**
	 * @return bool
	 */
	public function hasFullAccessToController($controller)
	{
		if(!$this->guessControllerClass($controller)) {
			return false;
		}

		if($this->isUserAdmin()) {
			return true;
		}

		if($this->isAllowedToAllController($controller)) {
			return true;
		}

		if($this->isAllowedToAuthenticatedController($controller) && $this->isUserAuthenticated()) {
			return true;
		}

		if(!$this->isUserAuthenticated()) {
			return false;
		}

		$controllerName = $this->makeCleanControllerName($controller);
		$hashKey = EnumBackendAccessResourceType::CONTROLLER . '.' . $controllerName;

		return isset($this->accessibleResourcesHashMap[$hashKey]);
	}


	/**
	 * @return bool
	 */
	public function hasAccessToControllerAction($controller, $actionName)
	{
		if(!$this->guessControllerClass($controller)) {
			return false;
		}

		if($this->hasFullAccessToController($controller)) {
			return true;
		}

		if(!$this->isUserAuthenticated()) {
			return false;
		}

		$controllerName = $this->makeCleanControllerName($controller);

		$hashKey =
			EnumBackendAccessResourceType::CONTROLLER_ACTION . '.' . $actionName
			. ':' .
			EnumBackendAccessResourceType::CONTROLLER . '.' . $controllerName
		;


		return isset($this->accessibleResourcesHashMap[$hashKey]);
	}


	/**
	 * @return bool
	 */
	public function isAllowedToAllController($controller)
	{
		if(!$controllerClass = $this->guessControllerClass($controller)) {
			return false;
		}

		$allowAccessToAll = false;

		try {
			$allowAccessToAll =  constant("$controllerClass::ALLOW_ACCESS_ALL");
		}
		catch (BaseException $e) {
			/* constant ALLOW_ACCESS_ALL is not defined */
			$allowAccessToAll = false;
		}

		return $allowAccessToAll;
	}


	/**
	 * @return bool
	 */
	protected function isAllowedToAuthenticatedController($controller)
	{
		if(!$controllerClass = $this->guessControllerClass($controller)) {
			return false;
		}

		$allowAccessToAuthenticated = false;

		try {
			$allowAccessToAuthenticated = constant("$controllerClass::ALLOW_ACCESS_AUTHENTICATED");
		}
		catch (BaseException $e) {
			/* constant ALLOW_ACCESS_AUTHENTICATED is not defined */
			$allowAccessToAuthenticated = false;
		}

		return $allowAccessToAuthenticated;
	}


	/**
	 * @return bool
	 */
	public function isSameUser($otherUser)
	{
		if(!$this->isUserAuthenticated() || !$otherUser instanceof BackendUser) {
			return false;
		}

		return $this->getAuthenticatedUser()->getId() == $otherUser->getId();
	}


	/**
	 * @return bool
	 */
	public function isSameUserOrAdmin($otherUser)
	{
		if($this->isUserSuperAdmin() || $this->isUserAdmin() ) {
			return true;
		}

		return $this->isSameUser($otherUser);
	}


	/**
	 * @return bool
	 */
	public function isUserSuperAdmin()
	{
		if(!$this->isUserAuthenticated()) {
			return false;
		}

		return $this->getAuthenticatedUser()->getIsSuperAdmin();
	}


	/**
	 * @return bool
	 */
	public function isUserAdmin($orSuperAdmin = true)
	{
		if($orSuperAdmin && $this->isUserSuperAdmin()) {
			return true;
		}

		if(!$this->isUserAuthenticated()) {
			return false;
		}

		$roleList = $this->getAuthenticatedUser()->getAccessRoles()->getList();

		/** @var BackendUserRole $role */
		foreach ($roleList as $role) {
			if($role->getName() == self::ADMIN_ROLE_NAME) {
				return true;
			}
		}

		return false;
	}

	public function hasRole($roleName)
	{
		if(!$this->isUserAuthenticated()) {
			return false;
		}

		$roleList = $this->getAuthenticatedUser()->getAccessRoles()->getList();

		/** @var BackendUserRole $role */
		foreach ($roleList as $role) {
			if($role->getName() == $roleName) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Load user permissions for faster access check
	 */
	private function setUpUser(BackendUser $user)
	{
		$this->authenticatedUser = $user;
		$this->accessibleResourcesByRole = $this->authenticatedUser->getAccessRoles()->getAllAccessibleResources();
		$this->accessibleResourcesHashMap = array();

		foreach($this->accessibleResourcesByRole as $role) {
			/** @var $resource BackendAccessResource */
			foreach($role['resources'] as $resource) {
				$hashKey = $resource->getType()->getName() . '.' . $resource->getName();

				$parent = $resource->getParent();
				if($parent) {
					$hashKey .= ':' . $parent->getType()->getName() . '.' . $parent->getName();
				}

				$this->accessibleResourcesHashMap[$hashKey] = $resource;
			}
		}
	}


	/**************************************************************
	*********************** MISC METHODS **************************
	**************************************************************/
	private function getUserCookieVarName()
	{
		return static::COOKIE_AUTHENTICATED_USER_VAR_PREFIX . SYSTEM_PROJECT_NAME;
	}


	/**
	 * @return string
	 */
	protected function makeCleanControllerName($controller, $getCleanName = false)
	{
		$controllerName = $controller;
		if ($controller instanceof Controller) {

			if($getCleanName) {
				if(method_exists($controller, 'getCleanName')) {
					$controllerName = $controller->getCleanName();
				}
				else {
					$controllerName = str_replace('Controller', '', get_class($controller));
				}
			}
			else {
				$controllerName = get_class($controller);
			}

			return $controllerName;
		}

		$controllerName = StringUtils::dashedToCamelCase($controllerName, true);

		if(!$getCleanName) {
			$controllerName = str_replace('Controller', '', $controllerName) . 'Controller';
		}

		return $controllerName;
	}

	/**
	 * @return null|string
	 */
	protected function guessControllerClass($controller)
	{
		if ($controller instanceof Controller) {
			return get_class($controller);
		}

		$controllerName = $this->makeCleanControllerName($controller, true);

		$controllerClass = $controllerName . 'Controller';

		if(!class_exists($controllerClass)) {
			$controllerClass = ucfirst($controllerName) . 'Controller';
			if(!class_exists($controllerClass)) {
				return null;
				//throw new ClassNotFoundException('Cannot resolve controller class for "' . $controllerName . '"');
			}
		}

		return $controllerClass;
	}

	/**
	 * @return BackendUser
	 */
	public function getAuthenticatedUser()
	{
		return $this->authenticatedUser;
	}

	protected function dropUserCookie()
	{
		$this->setUserCookie(null);
	}

	protected function setUserCookie($cookieValue = null)
	{
		Cookie::create($this->getUserCookieVarName())->
			setValue($cookieValue)->
			setDomain('.' . DOMAIN_NAME)->
			setPath('/')->
			setMaxAge(60 * 60 * 24 * static::COOKIE_AUTHENTICATED_USER_COOKIE_MAX_AGE_IN_DAYS)->
			setHttpOnly(true)->
			httpSet();
	}
}