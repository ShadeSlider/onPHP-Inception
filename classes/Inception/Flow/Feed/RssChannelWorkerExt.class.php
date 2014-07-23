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
/* $Id: RssChannelWorker.class.php 3 2009-02-17 12:02:51Z shimizu $ */

	/**
	 * @ingroup Feed
	**/
	final class RssChannelWorkerExt extends Singleton
	{
		/**
		 * @return RssChannelWorker
		**/
		public static function me()
		{
			return Singleton::getInstance(__CLASS__);
		}
		
		/**
		 * @return FeedChannel
		**/
		public function makeChannel(SimpleXMLElement $xmlFeed)
		{
			if (
				(!isset($xmlFeed->channel))
				|| (!isset($xmlFeed->channel->title))
			)
				throw new WrongStateException(
					'there are no channels in given rss'
				);
			
			$feedChannel =
				FeedChannel::create((string) $xmlFeed->channel->title);
			
			if (isset($xmlFeed->channel->link))
				$feedChannel->setLink((string) $xmlFeed->channel->link);
			
			return $feedChannel;
		}
		
		public function toXml(FeedChannelExt $channel, $itemsXml)
		{
			return
				'<?xml version="1.0" encoding="utf-8"?>'."\n".
				 '<rss version="'.RssFeedFormat::VERSION.'" xml:base="http://organicnatural.ru" xmlns:dc="http://purl.org/dc/elements/1.1/">'
					.'<channel>'
						.'<title>'.$channel->getTitle().'</title>'
						.(
							$channel->getLink()
								? '<link>'.$channel->getLink().'</link>'
								: null
						)
						.(
							$channel->getDescription()
								?
									'<description>'
									.$channel->getDescription()
									.'</description>'
								: null
						)
						."\n".
						$itemsXml
						.(
							$channel->getImage() 
								?
									'<image>
								      <url>'.$channel->getImage()->getUrl().'</url>
								      <link>'.$channel->getImage()->getLink().'</link>
								      <title>'.$channel->getImage()->getTitle().'</title>
								    </image>'
								: null    
						)
					.'</channel>'
				.'</rss>';
		}
	}
?>