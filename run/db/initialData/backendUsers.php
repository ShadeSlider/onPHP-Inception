<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

//admin
$backendUserAdmin =
	BackendUser::create()->
		setIsSuperAdmin(true)->
		setLogin('admin')->
		setPassword(md5('inception'))->
		setEmail('admin@onphp-inception.com')->
		setFirstName('I am')->
		setLastName('the Boss')->
		setGender('m')->
		setCreatedAt(TimestampTZ::makeNow())
;
$backendUserAdmin->dao()->take($backendUserAdmin);

//default_user
$backendUserDefault =
	BackendUser::create()->
		setLogin('default_user')->
		setPassword(md5('inception'))->
		setEmail('default@onphp-inception.com')->
		setFirstName('Default')->
		setLastName('User')->
		setGender('f')->
		setCreatedAt(TimestampTZ::makeNow())
;
$backendUserDefault->dao()->take($backendUserDefault);