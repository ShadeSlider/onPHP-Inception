<?php
/**
 * WebApplication class
 * @author Eric I. Gorbikov
 * @copyright 2014 Eric I. Gorbikov
 */
class WebApplication extends Singleton {

	protected $printDebug = false;
	const DEFAULT_CONTENT_TYPE = 'text/html';

	/** @var BackendAccessManager */
	protected $accessManager;

	/** @var WebApplicationConfig */
	protected $config;

	/** @var HttpRequest */
	protected $request;

	/** @var  ModelAndView */
	protected $mav;

	/** @var Controller */
	protected $mainController;

	/** @var MultiPrefixPhpViewResolver */
	protected $viewResolver;

	/** @var  Logger */
	protected $loggerWrapper;

	/** @var  HtmlMetaData */
	protected $htmlMetaData;

	/** @var MetaDataManager */
	protected $metaDataManager;



	/**
	 * @return WebApplication
	 */
	public static function me()
	{
		return self::getInstance(__CLASS__);
	}


	/**
	 *
	 */
	public function __construct()
	{
		$this->accessManager = BackendAccessManager::me();
		$this->config = WebApplicationConfig::create();
		$this->mav = ModelAndView::create();
		$this->ui = HtmlUI::create();
		$this->viewResolver = MultiPrefixPhpViewResolver::create()->setViewClassName('ExtendedPhpView');
		$this->loggerWrapper = Logger::create();
		$this->metaDataManager = MetaDataManager::create(DIR_METADATA);
	}


	/**
	 * @throws Exception
	 * @throws WrongArgumentException
	 * @return ModelAndView
	 */
	public function processRequest(HttpRequest $request)
	{
		$this->assertIsInitiated();
		$this->request = $request;

		$this->setRouteDataToRequestIfExists($request);
		$mav = $this->mav = $this->generateModelAndView($request);

		$model = $mav->getModel();
		$view = $mav->getView();

		while(true) {

			if($view instanceof RedirectToView) {
				break;
			}

			if($view instanceof CleanRedirectView) {
				$view = $this->resolveRedirectView($view);
				break;
			}

			if($view instanceof SimplePhpView) {
				break;
			}

			if(!empty($view) && is_string($view)) {
				$view = $this->resolveViewFromString($view);
				break;
			}

			if($this->mainController instanceof BaseController) {
				try {
					$view = $this->resolveViewFromString($this->getCurrentControllerAction() . 'Action');
					break;
				} catch(WrongArgumentException $e) {
					throw $e;
					/* cannot resolve view */
				}
			}

			$view = EmptyView::create();
			break;
		}


		if($this->mainController instanceof BaseController && $view instanceof ExtendedPhpView) {
			$view->setMetaData($this->mainController->getMetaData());
		}

		if($model->has('httpStatus')) {
			HeaderUtils::sendHttpStatus(
				new HttpStatus($model->get('httpStatus'))
			);
		}

		//@TODO Move html meta data to controller
		$this->htmlMetaData = HtmlMetaData::create($this->request, $this->mainController);
		if(defined('SYSTEM_HTML_TITLE_PREFIX')) {
			$this->htmlMetaData->setTitlePrefix(SYSTEM_HTML_TITLE_PREFIX);
		}
		$model->set('metaData', $this->htmlMetaData);

		$contentType = $model->has('contentType') ? $model->get('contentType') : self::DEFAULT_CONTENT_TYPE;
		$this->sendContentTypeHeader($contentType);

		$view->render($model);
	}


	/**
	 * @throws WrongStateException
	 */
	private function assertIsInitiated()
	{
		if (!$this->isInitiated()) {
			throw new WrongStateException('Config, viewResolver and loggerWrapper must be set.');
		}
	}


	/**
	 * @return HttpRequest
	 */
	protected function setRouteDataToRequestIfExists(HttpRequest $request = null)
	{
		if($request === null) {
			$request = $this->request;
		}

		try {
			$request->
			setAttachedVar('routeName', RouterRewrite::me()->getCurrentRouteName())->
			setAttachedVar('route', RouterRewrite::me()->getCurrentRoute());
		} catch (RouterException $e) {
			/* invalid or empty route */
		}

		return $request;
	}


	/**
	 * @return ModelAndView
	 */
	public function generateModelAndView(HttpRequest $request)
	{
		$this->assertIsInitiated();
		$this->request = $request;
		$accessManager = $this->accessManager;


		if(!$this->mainController instanceof Controller) {
			$this->mainController = $this->resolveController($request);
		}

		$accessManager->autoAuthenticate();

		if(
			!$accessManager->
				hasAccessToControllerAction(
					$this->getMainControllerCleanName(),
					$this->getCurrentControllerAction()
				)

		) {
			if($accessManager->isUserAuthenticated()) {
				$this->mainController = new AccessDeniedController();
			}
			else {
				if(!Session::exist('requestedUrl')) {
					Session::assign('requestedUrl', Utils::getCurrentURL());
				}
				$this->mainController = new AuthController();
				$this->request = HttpRequest::create();
			}
		}

		$mav = $this->mainController->handleRequest($this->request);

		$model = $mav->getModel();
		$model->set('loggedInUser', $accessManager->getAuthenticatedUser());
		$model->set('controllerName', strtolower($this->getMainControllerCleanName()));
		$model->set('controllerNameDashed', StringUtils::camelCaseToDashed($this->getMainControllerCleanName()));
		$model->set('controllerNameClean', $this->getMainControllerCleanName());
		$model->set('controllerActionName', strtolower($this->getCurrentControllerAction()));

		return $mav;
	}


	/**
	 * @throws BaseException
	 */
	protected function sendContentTypeHeader($mediaType, $charset = null, $silent = true)
	{
		try {
			$contentTypeHeader = ContentTypeHeader::create()->setMediaType($mediaType);

			if(is_string($charset)) {
				$contentTypeHeader->setCharset($charset);
			}
			elseif (defined('HTML_CHARSET')) {
				$contentTypeHeader->setCharset(HTML_CHARSET);
			}


			HeaderUtils::sendContentType($contentTypeHeader);
		} catch (BaseException $e) {
			/* Headers already sent */
			if(!$silent) {
				throw $e;
			}
		}
	}


	/**
	 * @param HttpRequest $request
	 * @throws WrongArgumentException
	 * @throws WrongStateException
	 * @return Controller
	 */
	public function resolveController(HttpRequest $request)
	{
		$this->assertIsInitiated();

		$this->request = $request;
		$controllerName = '';

		if($request->hasAttachedVar(SYSTEM_CONTROLLER_VAR_NAME)) {
			$controllerName = $request->getAttachedVar(SYSTEM_CONTROLLER_VAR_NAME);
		}

		if(!$controllerName && $request->hasGetVar(SYSTEM_CONTROLLER_VAR_NAME)) {
			$controllerName = $request->getGetVar(SYSTEM_CONTROLLER_VAR_NAME);
		}

		if(!$controllerName && $request->hasPostVar(SYSTEM_CONTROLLER_VAR_NAME)) {
			$controllerName = $request->getPostVar(SYSTEM_CONTROLLER_VAR_NAME);
		}

		if($controllerName != '') {
			$controllerName = StringUtils::dashedToCamelCase(ucfirst($controllerName)) . 'Controller';
		}

		if(!ClassUtils::isClassName($controllerName) || !class_exists($controllerName)) {
			$controllerName = $this->config->getSetting(WebApplicationConfig::NOT_FOUND_CONTROLLER_VAR);
		}


		return new $controllerName($this->metaDataManager);
	}


	/**
	 * @return string
	 */
	private function getMainControllerCleanName()
	{
		$controller = $this->mainController;

		if(method_exists($controller, 'getCleanName')) {
			return $controller->getCleanName();
		}

		return str_replace('Controller', '', get_class($controller));
	}


	private function getCurrentControllerAction()
	{
		$request = $this->request;
		$actionIndexName = 'action';
		$currentAction = 'unknown';

		while(true) {
			if(method_exists($this->mainController, 'chooseAction')) {
				$currentAction =  $this->mainController->chooseAction($request);
				break;
			}

			if(method_exists($this->mainController, 'getCurrentAction')) {
				$currentAction =  $this->mainController->getCurrentAction();
				break;
			}

			if($request->hasAttachedVar($actionIndexName)) {
				$currentAction = $request->getAttachedVar($actionIndexName);
				break;
			}
			if($request->hasGetVar($actionIndexName)) {
				$currentAction = $request->getGetVar($actionIndexName);
				break;
			}
			if($request->hasPostVar($actionIndexName)) {
				$currentAction = $request->getPostVar($actionIndexName);
				break;
			}

			break;
		}


		return $currentAction;
	}


	protected function resolveRedirectView(CleanRedirectView $view)
	{
		if(strpos($view->getUrl(), 'http') !== false) {
			return $view;
		}

		$resolvedUrl = RouterRewrite::me()->getRoute($view->getUrl())->assembly();
		$resolvedViewClass = get_class($view);
		$resolvedView = new $resolvedViewClass($resolvedUrl);

		return $resolvedView;
	}


	/**
	 * @return View
	 */
	private function resolveViewFromString($viewName)
	{
		$controllerCleanName = strtolower(str_replace('Controller', '', get_class($this->mainController)));
		$controllerCleanFullName = $controllerCleanName . 'Controller';
		$controllerFullName = get_class($this->mainController);


		$this->viewResolver->dropPrefixes();
		$this->viewResolver->addPrefix(DIR_TEMPLATES . 'controllers' . DS . $controllerFullName . DS);
		$this->viewResolver->addPrefix(DIR_TEMPLATES . 'controllers' . DS . $controllerCleanFullName . DS);
		$this->viewResolver->addPrefix(DIR_TEMPLATES . 'controllers' . DS . $controllerCleanName . DS);
		$this->viewResolver->addPrefix(DIR_TEMPLATES . 'controllers' . DS);
		$this->viewResolver->addPrefix(DIR_TEMPLATES_TINY);
		$this->viewResolver->addPrefix(DIR_TEMPLATES . 'parts' . DS);
		$this->viewResolver->addPrefix(DIR_TEMPLATES);

		if($this->printDebug) {
			$this->viewResolver->setViewClassName('ExtendedDebugPhpView');
		}

		return $this->viewResolver->resolveViewName($viewName);
	}


	/**
	 * @return bool
	 */
	public function isInitiated()
	{
		if(!empty($this->config) && !empty($this->viewResolver) && !empty($this->loggerWrapper)) {
			return true;
		}
		else {
			return false;
		}

	}


	/**************************************************************
	 ************************ Getters & Setters *******************
	 **************************************************************/
	/**
	 * @return static
	 */
	public function setConfig($config)
	{
		$this->config = $config;
		return $this;
	}

	/**
	 * @return \WebApplicationConfig
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @return static
	 */
	public function setMainController($mainController)
	{
		$this->mainController = $mainController;
		return $this;
	}

	/**
	 * @return \Controller
	 */
	public function getMainController()
	{
		return $this->mainController;
	}

	/**
	 * @return static
	 */
	public function setMav($mav)
	{
		$this->mav = $mav;
		return $this;
	}

	/**
	 * @return \ModelAndView
	 */
	public function getMav()
	{
		return $this->mav;
	}


	/**
	 * @return ViewResolver
	 */
	public function getViewResolver()
	{
		return $this->viewResolver;
	}

	/**
	 * @return static
	 */
	public function setViewResolver($viewResolver)
	{
		$this->viewResolver = $viewResolver;
		return $this;
	}

	/**
	 * @return Logger
	 */
	public function getLoggerWrapper()
	{
		return $this->loggerWrapper;
	}

	/**
	 * @return static
	 */
	public function setLoggerWrapper($loggerWrapper)
	{
		$this->loggerWrapper = $loggerWrapper;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPrintDebug()
	{
		return $this->printDebug;
	}

	/**
	 * @return static
	 */
	public function setPrintDebug($printDebug)
	{
		$this->printDebug = (boolean)($printDebug);
		return $this;
	}

	/**
	 * @return MetaDataManager
	 */
	public function getMetaDataManager()
	{
		return $this->metaDataManager;
	}

	/**
	 * @return static
	 */
	public function setMetaDataManager($metaDataManager)
	{
		$this->metaDataManager = $metaDataManager;
		return $this;
	}
}