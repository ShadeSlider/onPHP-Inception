<?php
/***************************************************************************
 *   Copyright (C) 2005 by Sergey S. Sergeev                               *
 *   webs.support(no@spam)gmail.com                                        *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU General Public License as published by  *
 *   the Free Software Foundation; either version 2 of the License, or     *
 *   (at your option) any later version.                                   *
 *                                                                         *
 ***************************************************************************/
/* $Id: HtmlEntitiesDecode.class.php 479 2008-07-18 12:56:34Z ssserj $ */

	final class HtmlEntitiesDecode extends HtmlEntitiesFilter
	{
		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}

		protected function __construct()
		{
			$this->setHtml(false);
			$this->setDecode(true);
		}

		public function apply($value)
		{
			$value = html_entity_decode($value, $this->quoteStyle, $this->encoding);

			return parent::apply($value);
		}
	}
?>
