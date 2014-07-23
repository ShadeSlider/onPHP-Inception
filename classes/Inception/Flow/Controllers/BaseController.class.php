<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

abstract class BaseController extends MethodMappedController {

	const DEFAULT_ACTION_NAME = 'default';
	const DEFAULT_ACTION_METHOD_NAME = 'defaultAction';
	const DATA_SCOPE_NAME = 'DATA';
	const SEARCH_DATA_SCOPE_NAME = 'search';

	protected $currentAction;
	protected $currentActionMethodName;

	/** @var ModelAndView */
	protected $mav;

	/** @var HttpRequest */
	protected $request;

	/** @var  Form */
	protected $form;

	/** @var  Logger */
	protected $loggerWrapper;

	/** @var FlashMessageManager */
	protected $flashMessagesManager;

	/** @var BackendAccessManager */
	protected $accessManager;

	/** @var MetaDataManager */
	protected $metaDataManager;

	/** @var HtmlUI */
	protected $ui;

	/** @var  string */
	protected $routeName;

	/** @var RouterBaseRule */
	protected $route;

	protected $defaultListOrderBy = null;


	public function __construct($metaDataManager = null)
	{
		if(method_exists($this, static::DEFAULT_ACTION_METHOD_NAME)) {
			$this->setMethodMapping(static::DEFAULT_ACTION_NAME, static::DEFAULT_ACTION_METHOD_NAME);
			$this->setDefaultAction(static::DEFAULT_ACTION_NAME);

			$this->currentAction = static::DEFAULT_ACTION_NAME;
		}

		$this->mav = ModelAndView::create();
		$this->loggerWrapper = Logger::create();
		$this->accessManager = BackendAccessManager::me();
		$this->flashMessagesManager = FlashMessageManager::create();

		if($metaDataManager) {
			$this->metaDataManager = $metaDataManager;
		}
		else {
			$this->metaDataManager = MetaDataManager::create(DIR_METADATA);
		}

		$this->ui = HtmlUI::create($this);
	}


	/**
	 * @return ModelAndView
	 */
	public function handleRequest(HttpRequest $request)
	{
		$this->request = $request;
		$this->setRouteData($request);

		$chosenAction = $this->chooseAction($request);
		$chosenActionMethodName = $this->guessMethodNameByAction($chosenAction);
		$currentActionUCFirst = ucfirst($this->currentAction);
		$model = $this->mav->getModel();

		if($this->beforeAction($request)) {

			if(method_exists($this, 'setCommonDataToModel' . $currentActionUCFirst)) {
				$this->{'setCommonDataToModel' . $currentActionUCFirst}($model);
			}
			else {
				$this->setCommonDataToModel($model);
			}
			$this->mav = $this->{$chosenActionMethodName}($request);
			$this->afterAction($request);
		} else {
			$this->mav = ModelAndView::create();
		}

		$model->
			set('isAjaxRequest', AjaxUtils::me()->isAjaxRequest($request))->
			set('action', $this->getCurrentAction())->
			set('currentUrl', $this->getCurrentUrl($request))->
			set('defaultActionUrl', $this->defaultActionUrl($request))->
			set('currentActionUrl', $this->getCurrentUrl($request, true))->
			set('controllerName', $this->getCleanName())->
			set('actionName', $this->getCurrentAction())->
			set('flashMessagesManager', $this->flashMessagesManager)->
			set('controllerMetaData', $this->getMetaData())->
			set('breadCrumbs', $this->getBreadCrumbs())->
			set('ui', $this->ui)
		;


		if(!$this->mav->getModel()->has('pageTitle') || !$this->mav->getModel()->get('pageTitle')) {
			$this->mav->getModel()->set('pageTitle', $this->getPageTitle());
		}

		if(!$this->mav->getView()) {
			$this->mav->setView($this->getCurrentActionMethodName());
		}

		return $this->mav;
	}


	/**
	 * @return string|null
	 */
	public function chooseAction(HttpRequest $request)
	{
		$chosenAction = parent::chooseAction($request);

		if(empty($chosenAction) || $chosenAction == $this->getDefaultAction()) {

			$actionPrm = Primitive::string('action');
			Form::create()->
				add($actionPrm)->
				import($request->getGet())->
				importMore($request->getPost())->
				importMore($request->getAttached())
			;

			if($actionPrm->getValue()) {
				$chosenAction = $actionPrm->getValue();
			}
		}

		$this->currentAction = $chosenAction = StringUtils::dashedToCamelCase($chosenAction);

		return $chosenAction;
	}


	/**
	 * @return string
	 * @throws UnsupportedMethodException
	 */
	public function guessMethodNameByAction($chosenAction)
	{
		$chosenActionMethodName = '';
		$methodMapping = $this->getMethodMapping();
		if(
			isset($methodMapping[$chosenAction])
			&&
			method_exists($this, $methodMapping[$chosenAction])
		) {
			$chosenActionMethodName =  $methodMapping[$chosenAction];
		}

		if(method_exists($this, $chosenAction . 'Action')) {
			$chosenActionMethodName =  $chosenAction . 'Action';
		}

		if($chosenActionMethodName) {
			$this->currentActionMethodName = $chosenActionMethodName;
			return $chosenActionMethodName;
		}

		throw new UnsupportedMethodException('Action "' . $chosenAction . '" cannot be resolved.');
	}


	protected function beforeAction($request)
	{
		return true;
	}


	protected function afterAction($request) { }


	/**
	 * @return Model
	 */
	protected function setCommonDataToModel(Model $model)
	{
		return $model;
	}

	/**
	 * @throws WrongStateException
	 */
	public function log($message, $logLevelName = LogLevel::FINEST)
	{
		if(!$this->loggerWrapper instanceof Logger) {
			throw new WrongStateException();
		}

		if($message instanceof LogRecord) {
			$this->loggerWrapper->logRecord($message);
		}
		else {
			$logLevel = new LogLevel($logLevelName);
			$this->loggerWrapper->log($logLevel, $message);
		}
	}


	/**
	 * @return string
	 */
	public function getCleanName()
	{
		return str_replace('Controller', '', get_class(new static));
	}


	/**
	 * @return string
	 */
	public function getCleanDashedName()
	{
		return StringUtils::camelCaseToDashed($this->getCleanName());
	}


	/**
	 * @return string|null
	 */
	public function getCurrentAction()
	{
		return $this->currentAction;
	}


	/**
	 * @return mixed
	 */
	public function getCurrentActionMethodName()
	{
		return $this->currentActionMethodName;
	}


	/**
	 * @return Logger
	 */
	public function getLoggerWrapper()
	{
		return $this->loggerWrapper;
	}


	/**
	 * @return BaseController
	 */
	public function setLoggerWrapper($loggerWrapper)
	{
		$this->loggerWrapper = $loggerWrapper;
		return $this;
	}


	/**
	 * @return FlashMessageManager
	 */
	public function getFlashMessagesManager()
	{
		return $this->flashMessagesManager;
	}


	/**
	 * @return static
	 */
	public function setFlashMessagesManager($flashMessageManager)
	{
		$this->flashMessagesManager = $flashMessageManager;
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


	/**
	 * @return HtmlUI
	 */
	public function getUi()
	{
		return $this->ui;
	}


	/**
	 * @return static
	 */
	public function setUi($ui)
	{
		$this->ui = $ui;
		return $this;
	}


	/**
	 * @return string
	 */
	protected function getPageTitle($actionName = null)
	{
		if(!$actionName) {
			$actionName = $this->getCurrentAction();
		}

		$metaData = $this->getMetaData();

		$pageTitle = '';

		if(!empty($metaData['title'])) {
			$pageTitle = $metaData['title'];
		}

		if(!empty($actionName) && !empty($metaData['title' . ucfirst($actionName)])) {
			$pageTitle = $metaData['title' . ucfirst($actionName)];
		}

		return $pageTitle;
	}


	/**
	 * @return BackendAccessManager
	 */
	public function getAccessManager()
	{
		return $this->accessManager;
	}

	/**
	 * @return static
	 */
	public function setAccessManager($accessManager)
	{
		$this->accessManager = $accessManager;

		return $this;
	}


	/**
	 * @return CleanRedirectView
	 */
	public function makeRedirectViewForRoute($routeName, $routeParams = array(), $cleanRedirect = true)
	{
		$controllerName = isset($routeParams['controller']) ? $routeParams['controller'] : $this->getCleanDashedName();
		$actionName = isset($routeParams['action']) ? $routeParams['action'] : BaseController::DEFAULT_ACTION_NAME;

		if($this->route && $this->route->getDefault('action') !== null) {
			$actionName = $this->route->getDefault('action');
		}

		$routeParams = array_merge($routeParams, array('controller' => $controllerName, 'action' => $actionName));
		$url = $this->ui->url($routeName, $routeParams, true);

		$redirectView =
			$cleanRedirect
				? CleanRedirectView::create($url)
				: RedirectView::create($url);

		return $redirectView;
	}


	/**
	 * @return string
	 */
	protected function makeDataScope()
	{
		$dataScopeName = '';

		if (defined("static::DATA_SCOPE_NAME")) {
			$dataScopeName = BaseController::DATA_SCOPE_NAME;
			return $dataScopeName;
		}

		return $dataScopeName;
	}


	/**
	 * @return string
	 */
	public function getCurrentUrl(HttpRequest $request, $actionOnly = false)
	{
		if(!$request->hasAttachedVar('routeName')) {
			return PATH_WEB;
		}

		$routeName = $request->getAttachedVar('routeName');

		if(!$actionOnly) {
			return $this->ui->url($routeName, $request->getAttached());
		}
		else {
			return $this->ui->url($routeName, array('controller' => $this->getCleanDashedName(), 'action' => $this->getCurrentAction()));
		}
	}


	/**
	 * @return string
	 */
	public function defaultActionUrl(HttpRequest $request)
	{
		if(!$request->hasAttachedVar('routeName') || !$this->getDefaultAction()) {
			return PATH_WEB;
		}

		$routeName = $request->getAttachedVar('routeName');

		return $this->ui->url($routeName, array('controller' => $this->getCleanDashedName()), true);
	}


	protected function setRouteData(HttpRequest $request)
	{
		if(!$request->hasAttachedVar('route') || !$request->hasAttachedVar('routeName')) {
			return;
		}

		$this->routeName = $request->getAttachedVar('routeName');
		$this->route = $request->getAttachedVar('route');
	}


	/**
	 * @return Form
	 */
	protected function importFormDataFromRequestIntoForm(HttpRequest $request, $form = null, $forceDataScope = null)
	{
		if (!$form instanceof Form) {
			$form = $this->form;
		}

		$dataScopeName = $forceDataScope ? : $this->makeDataScope();

		if (!$forceDataScope) {
			$form->
				import($request->getAttached())->
				importMore($request->getGet())->
				importMore($request->getPost())->
				importMore($request->getFiles()) //@TODO Should it be here?
			;
		}

		$form->importMore(
			$request->hasAttachedVar($dataScopeName)
				? $request->getAttachedVar($dataScopeName)
				: $request->getAttached()
		);

		$form->importMore(
			$request->hasGetVar($dataScopeName)
				? $request->getGetVar($dataScopeName)
				: $request->getGet()
		);

		$form->importMore(
			$request->hasPostVar($dataScopeName)
				? $request->getPostVar($dataScopeName)
				: $request->getPost()
		);

		//@TODO Should it be here?
		$form->importMore(
			$request->hasFilesVar($dataScopeName)
				? $request->getFilesVar($dataScopeName)
				: $request->getFiles()
		);

		$form->checkRules();

		return $form;
	}


	/**
	 * @return string|array
	 */
	protected function getControllerMetaDataValueOrDefault($valueName, $defaultValue)
	{
		$controllerMetaData = $this->getMetaData();

		return isset($controllerMetaData[$valueName]) ? $controllerMetaData[$valueName] : $defaultValue;
	}


	/**
	 * @return string
	 */
	protected function getControllerNameForMetaData()
	{
		if (defined("static::META_DATA_CONTROLLER_NAME")) {
			return static::META_DATA_CONTROLLER_NAME;
		}

		return $this->getCleanName();
	}


	/**
	 * @return array|mixed|null
	 */
	public function getMetaData($controllerName = null)
	{
		$controllerName = $controllerName ?: $this->getControllerNameForMetaData();
		return $this->metaDataManager->getControllerMetaData($controllerName);
	}


	/**
	 * @return array
	 */
	protected function getBreadCrumbs($controllerName = null)
	{
		$controllerName = $controllerName ?: $this->getControllerNameForMetaData();
		return $this->metaDataManager->getControllerBreadCrumbs($controllerName, $this->getCurrentAction());
	}


	/**
	 * @return FlashMessageManager
	 */
	protected function addFlashMessageFromMetaData($messageName, $metaDataFieldName, $data)
	{
		$data = (array)$data;
		extract($data);

		return $this->flashMessagesManager->addMessage($messageName, sprintf($this->getControllerMetaDataValueOrDefault($metaDataFieldName, ''), $orderId));
	}


	/**
	 * @return CleanRedirectView
	 */
	protected function makeRedirectHomeView()
	{
		return CleanRedirectView::create(PATH_WEB);
	}


	/**
	 * @return bool
	 */
	protected function checkSameUser($orderBackendUser)
	{
		return $this->accessManager->isSameUserOrAdmin($orderBackendUser);
	}


	/**
	 * @return string
	 */
	protected function makeJSON($data)
	{
		if (!is_array($data)) {
			$data = (array)$data;
		}

		return JsonEncoderFilter::me()->apply($data);
	}


	/**
	 * @return array
	 */
	protected function makeDefaultJSONResponseArray()
	{
		$response = array(
			'status' => 1,
			'error' => 0,
			'message' => 'OK',
			'data' => new stdClass()
		);

		return $response;
	}


	/**
	 * @return Model
	 */
	protected function makeDefaultJSONModel(array $jsonData = array())
	{
		$defaultJsonData = $this->makeDefaultJSONResponseArray();
		$jsonData = array_merge($defaultJsonData, $jsonData);

		$jsonModel = Model::create()->set('ajaxResponse', JsonEncoderFilter::me()->apply($jsonData));

		return $jsonModel;
	}
}