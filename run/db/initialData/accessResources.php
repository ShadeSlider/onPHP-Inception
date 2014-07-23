<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

//IndexController
$resourceIndexController =
	BackendAccessResource::create()->
		setName('IndexController')->
		setTitle('Index Controller')->
		setType(EnumBackendAccessResourceType::controller())
;
$resourceIndexController->dao()->take($resourceIndexController);


//BackendUserController
$resourceBackendUserController =
	BackendAccessResource::create()->
		setName('BackendUserController')->
		setTitle('Backend User Controller')->
		setType(EnumBackendAccessResourceType::controller())
;
$resourceBackendUserController->dao()->take($resourceBackendUserController);