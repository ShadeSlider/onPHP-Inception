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
/* $Id: HtmlEntitiesFilter.class.php 479 2008-07-18 12:56:34Z ssserj $ */

	/**
	 * Base HTML Entities replacer.
	 *
	 * @see HtmlEntitiesEncode - for text import
	 * @see HtmlEntitiesDecode - for text import
	 * @see SmartHtmlEncode - for html import
	 *
	 * @ingroup Filters
	**/
	abstract class HtmlEntitiesFilter extends BaseFilter
	{
		/**
		 * @see http://ru.php.net/manual/ru/function.html-entity-decode.php
		 * @var bool
		 */
		protected $isDecode = false;

		/**
		 * array(
		 * 	'<simbol>'=>'html equivalent'
		 * )
		 *
		 * @var array
		 */
		protected static $entites = array(
			'№'	=> "&#8470;",
			'‚'	=> ',', //необычная запятая, XHTML='&#130;' HEX='С282'
			'–'	=> "&ndash;", //длинное тире XHTML='&#8211;' HEX='E28093'
			'«'	=> "&laquo;",
			'»'	=> "&raquo;",
			'©'	=> "&copy;",
			'¶'	=> "&para;",
			'®'	=> "&reg;",
			'”'	=> "&laquo;",
			'“'	=> "&raquo;",
			'•' => "&bull;",
			'’' => "&rsquo;",
			'‘'	=> "&lsquo;",
			'—'	=> "&mdash;",
			'™'	=> "&trade;",
			'°'	=> "&deg;",
			'´'	=> "&acute;",
			'µ'	=> "&micro;",
			'·'	=> "&middot;",
			'¸'	=> "&cedil;",
			'¼'	=> "&frac14;",
			'½'	=> "&frac12;",
			'¾'	=> "&frac34;",
			'ˆ'	=> "&circ;",
			'˜'	=> "&tilde;",
			'…'	=> "&hellip;",
			'‰'	=> "&permil;",
			'‹'	=> "&lsaquo;",
			'›'	=> "&rsaquo;",
			'€'	=> "&euro;",
			'†'	=> "&dagger;",
			'‡'	=> "&Dagger;",
			"\xe2\x96\xaa"	=> "&#9642;",
			"\xe2\x80\x9a"	=> ",",
			","	=> "&sbquo;", //XHTML='&#8218'
			'º'	=> "&#186;",
		);

		protected $quoteStyle	= ENT_QUOTES;
		protected $encoding		= 'UTF-8';
		protected $isHtml		= false;

		public function apply($value)
		{
			if (
				$this->isHtml === false
				&& $this->isDecode === false
			)
				$value = htmlentities($value, $this->quoteStyle, $this->encoding);

			$value = $this->replaceUnsupportedSymbols($value);

			return $value;
		}

		protected function setHtml($boolean = false)
		{
			$this->isHtml = $boolean === true;
			return $this;
		}

		protected function setDecode($boolean)
		{
			$this->isDecode = $boolean === true;
			return $this;
		}

		protected function replaceUnsupportedSymbols($value)
		{
			$table = $this->getTranslationTable();

			return $this->replaceByMap($table, $value);
		}

		protected function replaceByMap(&$arrayMap, $value)
		{
			return
				($this->isDecode)
					? str_replace(
						array_values($arrayMap), // html equivalents
						array_keys($arrayMap), //symbols
						$value
					)
					: str_replace(
						array_keys($arrayMap), //symbols
						array_values($arrayMap), // html equivalents
						$value
					);
		}

		protected function getTranslationTable()
		{
			return self::$entites;
		}
	}
?>
