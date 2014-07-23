<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

//admin
$roleAdmin =
	BackendUserRole::create()->
		setName('admin')->
		setTitle('Administrator')
;
$roleAdmin->dao()->take($roleAdmin);

//default
$roleDefaultUser =
	BackendUserRole::create()->
		setName('default')->
		setTitle('Default user')
;
$roleDefaultUser->dao()->take($roleDefaultUser);