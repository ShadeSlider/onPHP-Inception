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
/* $Id: FeedChannel.class.php 3 2009-02-17 12:02:51Z shimizu $ */

	/**
	 * @ingroup Feed
	**/
	final class FeedChannelImageExt
	{
		private $title			= null;
		private $link			= null;
		private $url	= null;
		
		/**
		 * @return FeedChannelImageExt
		**/
		public static function create($title)
		{
			return new self($title);
		}
		
		public function __construct($title)
		{
			$this->title = $title;
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		/**
		 * @return FeedChannelImageExt
		**/
		public function setTitle($title)
		{
			$this->title = $title;
			
			return $this;
		}
		
		public function getUrl()
		{
			return $this->url;
		}
		
		/**
		 * @return FeedChannelImageExt
		**/
		public function setUrl($url)
		{
			$this->url = $url;
			
			return $this;
		}
		
		public function getLink()
		{
			return $this->link;
		}
		
		/**
		 * @return FeedChannelImageExt
		**/
		public function setLink($link)
		{
			$this->link = $link;
			
			return $this;
		}
	}
?>