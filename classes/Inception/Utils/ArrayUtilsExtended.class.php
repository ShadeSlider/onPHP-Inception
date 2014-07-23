<?php
final class ArrayUtilsExtended extends ArrayUtils {

	/**
	 * @return null|array
	 */
	public static function cutElement(&$array, $key) {
		if(!isset($array[$key])) return null;
		
		$keyOffset = array_search($key, array_keys($array));
		$result = array_values(array_splice($array, $keyOffset, 1));
		return $result[0];
	}


	/**
	 * @return array
	 */
	public static function objectListToSimpleIndexed($list, $keyField, $valField = 'id')
	{
		$out = array();
		$keyGetterName = 'get'.ucfirst($keyField);
		$valGetterName = 'get'.ucfirst($valField);
		foreach($list as $item) {
			$out[$item->$keyGetterName()] = $item->$valGetterName();
		}
		
		return $out;
	}


	/**
	 * @return array
	 */
	public static function arrayToSimpleIndexed($list, $keyField = 'id', $valField = 'id')
	{
		$out = array();
		foreach($list as $item) {
			$out[$item[$keyField]] = $item[$valField];
		}

		return $out;
	}


	/**
	 * @return array
	 */
	public static function objectPropertyList($list, $property)
	{
		$out = array();
		
		foreach ($list as $obj) {
			$getterName = "get".ucfirst($property);
			$out[] = $obj->$getterName();
		}
		
		return $out;
	}


	/**
	 * @return array
	 */
	public static function enumListToIndexedArray($enumList)
	{
		return ArrayUtilsExtended::objectListToSimpleIndexed($enumList, 'id', 'name');
	}
}