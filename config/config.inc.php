<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

define('CONFIG_INCLUDED', true);
define('SYSTEM_PROJECT_NAME', 'ergocrm');

//Resolving environment
$environment = getenv(SYSTEM_PROJECT_NAME . '_ENV');
if(!$environment) {
	$environment = 'dev';
}

if($envList = glob('ENV_*')) {
	if($envFileName = array_shift($envList)) {
		$environment = strtolower(str_replace('ENV_', '', $envFileName));
	}
}


require_once 'const.' . $environment . '.inc.php';
require_once 'config.base.inc.php';
//Environment specific config
require_once 'config.' . $environment . '.inc.php';