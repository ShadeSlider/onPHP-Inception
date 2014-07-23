<?php
/***************************************************************************
 *   Copyright (C) 2008 by Alexey Denisov                                  *
 *   adenisov@fjproject.ru                                                 *
 ***************************************************************************/
/* $Id: FilterCutOffText.class.php 2948 2008-11-01 11:00:35Z volerog $ */

	final class FilterCutOffText extends BaseFilter
	{
		protected $stringLength = 60;

		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}

		/**
		 * @return FilterCutOffText
		 */
		public function setLength($length)
		{
			$this->stringLength = $length;
			return $this;
		}

		public function apply($value)
		{
			if (mb_strlen($value) <= $this->stringLength)
				return $value;
			$firstSpace = mb_strpos($value, ' ', $this->stringLength);
			return $firstSpace ? mb_substr($value, 0, $firstSpace).'...' : $value;
		}
	}
?>