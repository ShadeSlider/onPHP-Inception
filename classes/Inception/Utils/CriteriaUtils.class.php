<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class CriteriaUtils extends StaticFactory {

	/**
	 * @return Criteria
	 */
	public static function addFlagExpressions(Criteria $criteria, $isTrueFields = array(), $isFalseFields = array(), $isNullFields = array(), $isNotNullFields = array())
	{
		foreach ($isTrueFields as $fieldName) {
			$criteria->add(Expression::isTrue($fieldName));
		}
		foreach ($isFalseFields as $fieldName) {
			$criteria->add(Expression::isFalse($fieldName));
		}
		foreach ($isNullFields as $fieldName) {
			$criteria->add(Expression::isNull($fieldName));
		}
		foreach ($isNotNullFields as $fieldName) {
			$criteria->add(Expression::notNull($fieldName));
		}

		return $criteria;
	}


	/**
	 * @return Criteria
	 */
	public static function addDefaultFlags(Criteria $criteria, $isTrueFields = array('isActive', 'isVisible'), $isFalseFields = array('isDeleted'))
	{
		return static::addFlagExpressions($criteria, $isTrueFields, $isFalseFields);
	}


	/**
	 * @return Criteria
	 */
	public static function addDefaultVisibility(Criteria $criteria, $isTrueFields = array('isVisible'))
	{
		$criteria = static::addDefaultFlags($criteria);

		return static::addFlagExpressions($criteria, $isTrueFields);
	}
}