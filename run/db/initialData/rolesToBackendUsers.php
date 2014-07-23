<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
/** Assign roles to backend users */
//admin
$backendUserAdmin->
	getAccessRoles()->
	mergeList(array($roleAdmin))->save()
;

//default_user
$backendUserDefault->
	getAccessRoles()->
	mergeList(array($roleDefaultUser))->save()
;