<?php
/***************************************************************************
 *   Copyright (C) 2007 by Dmitry A. Lomash, Dmitry E. Demidov             *
 *                                                                         *
 *   This program is free software; you can redistribute it and/or modify  *
 *   it under the terms of the GNU Lesser General Public License as        *
 *   published by the Free Software Foundation; either version 3 of the    *
 *   License, or (at your option) any later version.                       *
 *                                                                         *
 ***************************************************************************/
/* $Id: RssFeedFormat.class.php 3 2009-02-17 12:02:51Z shimizu $ */

	/**
	 * @ingroup Feed
	**/
	final class RssFeedFormatExt extends FeedFormatExt
	{
		const VERSION = '2.0';
		
		/**
		 * @return RssFeedFormat
		**/
		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}
		
		/**
		 * @return RssChannelWorker
		**/
		public function getChannelWorker()
		{
			return RssChannelWorkerExt::me();
		}
		
		/**
		 * @return RssItemWorker
		**/
		public function getItemWorker()
		{
			return RssItemWorkerExt::me();
		}
		
		public function isAcceptable(SimpleXMLElement $xmlFeed)
		{
			return (
				($xmlFeed->getName() == 'rss')
				&& (isset($xmlFeed['version']))
				&& ($xmlFeed['version'] == self::VERSION)
			);
		}

	}
?>