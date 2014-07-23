<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2009-2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
class Utils extends StaticFactory {


	/**
	 * @return int
	 */
	public static function daysInMonth($month, $year){
		// calculate number of days in a month
		return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);
	}


	/**
	 * @return object
	 */
	public static function fillEntityObjectFromArray($object, array $values)  {

		foreach($values as $key => $value) {
			$methodName = 'set'.$key;
			if(method_exists($object, $methodName)) {
				$object->$methodName($value);
			}
		}

		return $object;
	}


	/**
	 * @return mixed
	 */
	public static function castTo(&$val, $type)
	{
		switch ($type) {
			case "integer":
			case "int":
				$val = (int)$val;
				break;
			case "real":
			case "double":
			case "float":
				$val = (float)$val;
				break;
			case "string":
				$val = (string)$val;
				break;
			case "bool":
			case "boolean":
				$val = (int)$val;
				$val = (boolean)$val;
				break;
			case "array":
				$val = (array)$val;
				break;
			case "object":
				$val = (object)$val;
				break;
			case "unset":
				$val = (unset)$val;
				break;
			case "binary":
				$val = (binary)$val;
				break;
			default:
				return false;
		}

		return true;
	}


	/**
	 * @return string
	 */
	public static function getCurrentURL()
	{
		if(!isset($_SERVER))
			return '';

		$protocol = 'http'.(empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on' ? 's' : ''));
		$serverName = $_SERVER['SERVER_NAME'];
		$serverPort = $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];
		$requestURI = $_SERVER['REQUEST_URI'];

		return $protocol.'://'.$serverName.$serverPort.$requestURI;
	}
}