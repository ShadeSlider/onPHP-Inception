<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

abstract class FakeDataGenerator {

	protected static $data = array();

	const DATA_ITEMS_NUM = 100;

	/**
	 * @return static
	 */
	public static function create()
	{
		return new static();
	}

	abstract public function makeData();

	protected function fillObjectFromProto(Prototyped $object, $excludes = array())
	{
		$proto = $object->proto();

		/** @var LightMetaProperty $property */
		foreach ($proto->getPropertyList() as $property) {
			if($this->valueExists($property->getName()) && !in_array($property->getName(), $excludes)) {
				try {
					$object->{$property->getSetter()}($this->getRandomValue($property->getName()));
				} catch (BaseException $e) {
					/* Something terrible has happened! */
				}
			}
		}

	}

	protected function getRandomValueFromList($list, $minIndex = 0, $maxIndex = null)
	{
		$maxIndexFinal = count($list) > 0 ? count($list)-1 : 0;

		if(is_integer($maxIndex)) {
			if($maxIndex < count($list)-1) {
				$maxIndexFinal = $maxIndex;
			}
		}

		$randomIndex = mt_rand($minIndex, $maxIndexFinal);

		return $list[$randomIndex];
	}

	protected function getRandomValue($name)
	{
		$dataVariants = static::$data[$name];
		$randomIndex = mt_rand(0, count($dataVariants)-1);

		return $dataVariants[$randomIndex];
	}

	protected function getRandomBooleanValue()
	{
		return (boolean)mt_rand(0, 1);
	}

	protected function getRandomInteger($min = null, $max = null)
	{
		return mt_rand($min, $max);
	}

	protected function valueExists($name)
	{
		return isset(static::$data[$name]);
	}
}
