<?php
/***************************************************************************
 *   Copyright (C) 2008 by Alexey Denisov                                  *
 *   adenisov@fjproject.ru                                                 *
 ***************************************************************************/
/* $Id: FilterIconv.class.php 3746 2009-02-12 17:11:34Z stalkerxey $ */

	final class FilterStripSlashes extends BaseFilter
	{
		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}
				
		public function apply($value)
		{
			return stripslashes($value);
		}
	}
?>