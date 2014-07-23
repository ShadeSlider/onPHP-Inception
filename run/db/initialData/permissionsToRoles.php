<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

/** Assign permissions to roles */
//default
$roleDefaultUser->
	getPermissions()->
		fetch()->
		setList(array($resourceIndexController))->save()
;