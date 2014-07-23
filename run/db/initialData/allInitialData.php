<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
if(!defined('CONFIG_INCLUDED')) {
	require_once '../../../config/config.inc.php';
}

if(!defined('INITIAL_DATA_DIR')) {
	define('INITIAL_DATA_DIR', './');
}

include_once(INITIAL_DATA_DIR . 'generalClassifier.php');

include_once(INITIAL_DATA_DIR . 'accessResources.php');

include_once(INITIAL_DATA_DIR . 'backendUsers.php');

include_once(INITIAL_DATA_DIR . 'backendUserRoles.php');

include_once(INITIAL_DATA_DIR . 'permissionsToRoles.php');

include_once(INITIAL_DATA_DIR . 'rolesToBackendUsers.php');