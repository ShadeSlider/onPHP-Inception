<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

//SYSTEM CONSTANTS
define('SYSTEM_SESSION_NAME', 'ergocrm');
define('SYSTEM_CONTROLLER_VAR_NAME', 'controller');
define('SYSTEM_HTML_TITLE_PREFIX', 'onPHP Inception');

define('DEFAULT_NOT_FOUND_CONTROLLER_NAME', 'NotFoundController');
define('DEFAULT_AUTH_CONTROLLER_NAME', 'AuthController');
define('DEFAULT_INTERNAL_ERROR_CONTROLLER_NAME', 'InternalErrorController');
define('EMAIL_BUGS', 'bugs@onphp-inception.com');

//BASIC DIR & PATH CONSTANTS
//DIR_* - system path, PATH_* - url
define('DS', DIRECTORY_SEPARATOR);
define('DIR_BASE', realpath(dirname(dirname(__FILE__))).DS);
define('PATH_BASE', DIR_BASE);
define('DIR_METADATA', DIR_BASE.'metaData'.DS);
define('DIR_CLASSES', DIR_BASE.'classes'.DS);
define('PATH_CLASSES', DIR_CLASSES); //BC for meta generation
define('DIR_CONTROLLERS', DIR_BASE.'controllers'.DS);
define('DIR_TEMPLATES', DIR_BASE.'templates'.DS);
define('DIR_TEMPLATES_TINY', DIR_TEMPLATES.'tiny'.DS);
define('DIR_WEB',  DIR_BASE.'www'.DS);

define('DIR_EXTERNALS', DIR_BASE.'externals'.DS);
define('DIR_WWW_EXTERNALS', DIR_WEB.'externals'.DS);
;
define('DIR_FILES', DIR_BASE.'files'.DS);
define('DIR_IMAGES', DIR_BASE.'www'.DS.'img'.DS);


//WEB PATH CONSTANTS
define('PATH_WEB_CSS', PATH_WEB.'css/');
define('PATH_WEB_IMG', PATH_WEB.'img/');
define('PATH_WEB_JS',  PATH_WEB.'js/');
define('PATH_WEB_FILES',  PATH_WEB.'files/');
define('PATH_WEB_EXTERNALS', PATH_WEB.'externals/');

define('PATH_ENTRY_SCRIPT', PATH_WEB.'index.php');




//Basic initialization
//Encodings, locales, timezones, formats
putenv("TZ=Europe/Moscow");
date_default_timezone_set("Europe/Moscow");
setlocale(LC_ALL, "ru_RU.UTF8");
define('DATE_FORMAT', 'd.m.Y');
define('DATETIME_FORMAT', 'd.m.Y h:i');
define('PRICE_DECIMALS', 2);
define('PRICE_DEC_POINT', ',');

define('DEFAULT_ENCODING', 'UTF-8');
define('HTML_CHARSET', 'utf-8');

mb_internal_encoding(DEFAULT_ENCODING);
mb_regex_encoding(DEFAULT_ENCODING);

//The rest
ini_set('session.cookie_domain', '.'.DOMAIN_NAME );


//onPHP
define('ONPHP_CLASS_CACHE_TYPE', 'AutoloaderClassPathCache');
require DIR_EXTERNALS."onphp-extended/global.inc.php.tpl";

AutoloaderPool::get('onPHP')->
addPaths(array(
		DIR_CLASSES.DS,
		DIR_CLASSES.'DAOs'.DS,
		DIR_CLASSES.'Business'.DS,
		DIR_CLASSES.'Proto'.DS,
		DIR_CLASSES.'Filters'.DS,
		DIR_CLASSES.'Utils'.DS,
		DIR_CLASSES.'ViewHelpers'.DS,
		DIR_CLASSES.'Flow'.DS,
		DIR_CLASSES.'Flow'.DS.'FakeData'.DS,
		DIR_CLASSES.'Flow'.DS.'Mailer'.DS,
		DIR_CLASSES.'Flow'.DS.'Filters'.DS,
		DIR_CLASSES.'Flow'.DS.'Exchange'.DS,
		DIR_CLASSES.'Utils'.DS,

		DIR_CLASSES.'Utils'.DS.'Routers'.DS,
		DIR_CLASSES.'Utils'.DS.'Logging'.DS,

		DIR_CLASSES.'Auto'.DS.'Business'.DS,
		DIR_CLASSES.'Auto'.DS.'Proto'.DS,
		DIR_CLASSES.'Auto'.DS.'DAOs'.DS,

		DIR_CONTROLLERS.DS
));

//Inception sub-framework
require DIR_CLASSES.DS.'Inception'.DS.'autoload.inc.php';


//Routing
require_once 'config.router.inc.php';


//DB
PostgresDialect::setTsConfiguration("default_russian"); // tsearch2
PostgresDialect::setRankFunction("ts_rank");
DBPool::me()->setDefault(DB::spawn('PgSQL', DB_USER, DB_PASSWORD, DB_HOST, DB_NAME));