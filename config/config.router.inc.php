<?php
define('ROUTER_DEFINED', '');

RouterRewrite::me()->
	setBaseUrl(
		HttpUrl::create()->parse(PATH_WEB)
	)->
	/*addRoute(
		'entity',
		RouterRegexpRule::create('^entity\/([a-zA-Z0-9-]+)\/?([a-zA-Z0-9-]+)?')->
			setMap(array(
				0 => SYSTEM_CONTROLLER_VAR_NAME,
				1 => 'action'
			))->

			setReverse(PATH_WEB . 'entity/%s/%s')
	)->*/
	 /**************************************************************/
	addRoute(
		'index',
		RouterRegexpRule::create('^$|^index.php$')->
			setDefaults(array(
				SYSTEM_CONTROLLER_VAR_NAME => 'index'
			))->
			setReverse(PATH_WEB)
	)->
	addRoute(
		'common',
		RouterTransparentRule::create(':' . SYSTEM_CONTROLLER_VAR_NAME . '/:action/:offset')->
			setDefaults(
				array(
					'action' => '',
					'offset' => 0
				)
			)->
			setRequirements(
				array(
					SYSTEM_CONTROLLER_VAR_NAME => '[a-zA-Z0-9-]+',
					'action' => '[a-zA-Z0-9-]+',
					'offset' => '[0-9]+'
				)
			)
	)->
	/**************************************************************
	 ************************* ENTITIES ***************************
	 **************************************************************/
	addRoute(
		'entity',
		RouterTransparentRule::create('entity/:' . SYSTEM_CONTROLLER_VAR_NAME . '/:action/:offset')->
			setDefaults(
				array(
					'action' => '',
					'offset' => 0
				)
			)->
			setRequirements(
				array(
					SYSTEM_CONTROLLER_VAR_NAME => '[a-zA-Z0-9-]+',
					'action' => '[a-zA-Z0-9-]+',
					'offset' => '[0-9]+'
				)
			)
	)->
	addRoute(
		'internal-error',
		RouterStaticRule::create('internal-error')->
			setDefaults(array(
				SYSTEM_CONTROLLER_VAR_NAME => 'internalError'
			))
	)->
	addRoute(
		'login',
		RouterStaticRule::create('login')->
			setDefaults(array(
				SYSTEM_CONTROLLER_VAR_NAME => 'auth'
			))
	)->
	addRoute(
		'logout',
		RouterStaticRule::create('logout')->
			setDefaults(array(
				SYSTEM_CONTROLLER_VAR_NAME => 'auth',
				'action' => 'logout'
			))
	)
;