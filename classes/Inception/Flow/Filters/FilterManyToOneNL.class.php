<?
/***************************************************************************
 *   Copyright (C) 2008 by Alexey Denisov                                  *
 *   adenisov@fjproject.ru                                                 *
 ***************************************************************************/
/* $Id: FilterManyToOneNL.class.php 479 2008-07-18 12:56:34Z ssserj $ */

	final class FilterManyToOneNL extends BaseFilter
	{
		/**
		 * @return NewLinesToBreaks
		**/
		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}

		public function apply($value)
		{
			return preg_replace("/[\t\r\n\s]*\n+[\t\r\n\s]*/u", "\n", $value);
		}
	}

?>
