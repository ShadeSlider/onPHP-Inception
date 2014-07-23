<?php
/***************************************************************************
 *   Copyright (C) 2008 by Alexey Denisov                                  *
 *   adenisov@fjproject.ru                                                 *
 ***************************************************************************/
/* $Id: FilterIconv.class.php 3746 2009-02-12 17:11:34Z stalkerxey $ */

	final class FilterIconv extends BaseFilter
	{
		protected $fromEncoding = 'utf-8';
		protected $toEncoding = 'utf-8';

		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}

		/**
		 * @param string $fromEncoding
		 * @return FilterIconv
		 */
		public function setFromEncoding($fromEncoding)
		{
			Assert::isString($fromEncoding);
			$this->fromEncoding = $fromEncoding;
			return $this;
		}

		/**
		 * @param string $toEncoding
		 * @return FilterIconv
		 */
		public function setToEncoding($toEncoding)
		{
			Assert::isString($toEncoding);
			$this->toEncoding = $toEncoding;
			return $this;
		}

		public function apply($value)
		{
			return iconv($this->fromEncoding, $this->toEncoding, $value);
		}
	}
?>