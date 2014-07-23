<?php

try {
	require '../config/config.inc.php';

	session_name(SYSTEM_SESSION_NAME);
	Session::start();

	$config = WebApplicationConfig::create();
	$request = HttpRequest::createFromGlobals();
	$viewResolver = MultiPrefixPhpViewResolver::create();
	$loggerWrapper = Logger::create();

	RouterRewrite::me()->route($request);

	$webApplication =
		WebApplication::me()->
			setConfig($config)->
			setLoggerWrapper($loggerWrapper)

	;

	if(defined('__LOCAL_DEBUG__')) {
		$webApplication->setPrintDebug(true);
	}

	$webApplication->processRequest($request);
} catch (Exception $e) {
	$uri = 'Console(' . __FILE__ . '): ';

	if(isset($_SERVER)) {
		$uri = $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
	}

	$msg =
		'class: '.get_class($e)."\n"
		.'code: '.$e->getCode()."\n"
		.'message: '.$e->getMessage()."\n\n"
		.$e->getTraceAsString()."\n"
		."\n_POST=".var_export($_POST, true)
		."\n_GET=".var_export($_GET, true)
		.(
		isset($_SERVER['HTTP_REFERER'])
			? "\nREFERER=".var_export($_SERVER['HTTP_REFERER'], true)
			: null
		);

	if (defined('__LOCAL_DEBUG__')) {
		if(!empty($_SERVER)) {
			echo '<div style="background: #000; color: #efefef;">';
			echo '<pre>'.$msg.'</pre>';
			echo '</div>';
		}
		else {
			echo $msg;
		}
	}
	else {
		mail(EMAIL_BUGS, $uri, $msg);
		DBPool::me()->shutdown();

		HeaderUtils::redirectRaw(
			RouterUrlHelper::url(array(), 'internal-error')
		);
	}
}


exit;

try {

	$conf = GlobalConfig::create()
		->setSetting('test', 12312321)
		->lock()
	;


	viii($conf->getSetting('test'));

	viii($accessManager->hasAccessToControllerAction('index', 'index'));

	/**
	 * Currency update
	 */
//		GlobalData::updateCurrency();


	$request =
		HttpRequest::create()->
			setGet($_GET)->
			setPost($_POST)->
			setCookie($_COOKIE)->
			setServer($_SERVER)->
			setSession($_SESSION)->
			setFiles($_FILES);


	PartnerAccess::initialize($request);

	/**
	 * @todo Implement via access manager
	 */

	if($request->hasCookieVar('LU_'.DB_NAME)) {
		Session::assign('loggedUser', User::dao()->getById(base64_decode($request->getCookieVar('LU_'.DB_NAME))));
	}


	$controllerName = DEFAULT_CONTROLLER;

	RouterRewrite::me()->route($request);

//		vi($request);
	if (
		isset($_GET['area'])
		&& ClassUtils::isClassName($_GET['area'])
		&& defined('PATH_CONTROLLERS')
	) {
		$controllerName = $_GET['area'];
//			vi($controllerName);
	} elseif (
		$request->hasAttachedVar('area')
		&& ClassUtils::isClassName($request->getAttachedVar('area'))
	) {
		$controllerName = $request->getAttachedVar('area');
	} elseif (
		isset($_GET['area'])
		|| (
			isset($_SERVER['REQUEST_URI'])
			&& $_SERVER['REQUEST_URI'] != '/'
			&& $controllerName == DEFAULT_CONTROLLER
			&& parse_url(PATH_WEB, PHP_URL_PATH) != $_SERVER['REQUEST_URI']
		)
	) {
		$controllerName = DEFAULT_CONTROLLER;
		// таким образом, запросили модуль, которого нет на нашем сайте
		// защита от SEO-спама
		HeaderUtils::sendHttpStatus(
			new HttpStatus(HttpStatus::CODE_404)
		);
	}
//		GlobalValues::me()->setValue('currentNode', CMSNode::getByNameUrl(DEFAULT_NAME_URL));

//		vi($controllerName);
//							mp($request);

//			vi($request);
	/*		while(!empty($_GET['area']) || $request->hasAttachedVar('area'))
			{
				if(
					$request->hasAttachedVar('area')
					&&
					ClassUtils::isClassName($request->getAttachedVar('area'))
				) {
					$controllerName = $request->getAttachedVar('area');
					break;
				}

				$area = $_GET['area'];
	//			if(!is_readable(PATH_CONTROLLERS.DIRECTORY_SEPARATOR.$area.EXT_CLASS) || !ClassUtils::isClassName($area) || !class_exists($area)) break;

				$controllerName = $area;
				break;
			}

			if(!empty($request->getServerVar('REQUEST_URI')) && $area == DEFAULT_CONTROLLER) {
				//
			}*/

	//$controller = new
	/*die();
	if (
		isset($_GET['area']) && ClassUtils::isClassName($_GET['area'])
		&& defined('PATH_CONTROLLERS')
		&& (
			is_readable(PATH_CONTROLLERS.DIRECTORY_SEPARATOR.$_GET['area'].EXT_CLASS)
		)
	) {
		$controllerName = $_GET['area'];
	}

*/

	//Explicite user login
	//setcookie('LU_'.DB_NAME, base64_encode(71368), time()+60*60*24*30, '/', '.'.$request->getServerVar('HTTP_HOST'));

	$actionForm = Form::create()->
		add(Primitive::string('action'))->
		import($request->getGet())->
		import($request->getPost())->
		importMore($request->getAttached());

//vi($request->getAttached());

	$prefix = PATH_WEB.'?area=';
	$controller = new $controllerName;
	//$controller    = new AuthFilter(new $controllerName);
	$controllerResponce  = $controller->handleRequest($request);
	if($controllerResponce instanceof LayoutComposition) {

		foreach($controllerResponce->getList() as $lidx => $layout) {
			$layout->
				getModel()->
				set('selfUrl', PATH_WEB.'?area='.$controllerName)->
				set('baseUrl', PATH_WEB)->
				set('controllerName', $controllerName)->
				set('action', $actionForm->getValue('action'))->
				set('request', $request);



		}

		$view = new LayoutView($controllerResponce, LayoutViewResolver::create(PATH_TEMPLATES));
		$view->render();

	}
	else {
		$view 	= $controllerResponce->getView();
		$model 	= $controllerResponce->getModel();


		header("Pragma: no-cache");

		while(true)
		{
			if(!$view) {
				$view = $controllerName;
				break;
			}

			if(is_string($view) && @strpos($view, 'redirect:') !== false) {
				list(, $area) = explode(':', $view, 2);
				$view = new RedirectView(PATH_WEB.'?area='.$area);
				break;
			}

			if($view instanceof RedirectToView) {
				$view->setPrefix($prefix);
				break;
			}

			break;
		}
		if (!$view instanceof View) {
			header("Content-type: text/html; charset=utf-8");
			$viewName = $view;
			$viewResolver =
				MultiPrefixPhpViewResolver::create()->
					setViewClassName('SimplePhpView')->
					addPrefix(
						PATH_TEMPLATES.DIRECTORY_SEPARATOR
					);

			$view = $viewResolver->resolveViewName($viewName);
		}
		if (!$view instanceof RedirectView) {
			$model->
				set('selfUrl', PATH_WEB.'?area='.$controllerName)->
				set('baseUrl', PATH_WEB)->
				set('controllerName', $controllerName);

			$model->
				set('pathPrefix', $prefix)->
				set('pathController', $prefix.$controllerName)->
				set('controllerName', $controllerName)->
				set('request', $request);
		}

		$view->render($model);
	}

} catch (Exception $e) {

	$uri = 'Console: ';

	if(!isset($_SERVER)) {
		$uri = $_SERVER['HTTP_HOST'].$_SERVER["REQUEST_URI"];
	}

	$msg =
		'class: '.get_class($e)."\n"
		.'code: '.$e->getCode()."\n"
		.'message: '.$e->getMessage()."\n\n"
		.$e->getTraceAsString()."\n"
		."\n_POST=".var_export($_POST, true)
		."\n_GET=".var_export($_GET, true)
		.(
		isset($_SERVER['HTTP_REFERER'])
			? "\nREFERER=".var_export($_SERVER['HTTP_REFERER'], true)
			: null
		)
		/*.(
			isset($_SESSION) ?
				"\n_SESSION=".var_export($_SESSION, true)
				: null
		)*/;

	if (defined('__LOCAL_DEBUG__'))
		echo '<pre>'.$msg.'</pre>';
	else {
		mail(EMAIL_BUGS, $uri, $msg);
		DBPool::me()->shutdown();
		/*if (!HeaderUtils::redirectBack())
			HeaderUtils::redirectRaw('/');*/
	}
	//HeaderUtils::redirectRaw(PATH_WEB);
}