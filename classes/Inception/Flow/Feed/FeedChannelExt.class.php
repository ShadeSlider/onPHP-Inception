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
	final class FeedChannelExt
	{
		private $title			= null;
		private $link			= null;
		private $description	= null;
		private $image	= null;
		private $feedItems		= array();
		
		/**
		 * @return FeedChannelExt
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
		 * @return FeedChannelExt
		**/
		public function setTitle($title)
		{
			$this->title = $title;
			
			return $this;
		}
		
		/**
		 * @return FeedChannelImageExt
		**/		
		public function getImage()
		{
			return $this->image;
		}
		
		/**
		 * @return FeedChannelExt
		**/
		public function setImage(FeedChannelImageExt $image)
		{
			$this->image = $image;
			
			return $this;
		}
		
		public function getDescription()
		{
			return $this->description;
		}
		
		/**
		 * @return FeedChannelExt
		**/
		public function setDescription($description)
		{
			$this->description = $description;
			
			return $this;
		}
		
		public function getLink()
		{
			return $this->link;
		}
		
		/**
		 * @return FeedChannelExt
		**/
		public function setLink($link)
		{
			$this->link = $link;
			
			return $this;
		}
		
		public function getFeedItems()
		{
			return $this->feedItems;
		}
		
		/**
		 * @return FeedChannelExt
		**/
		public function setFeedItems($feedItems)
		{
			$this->feedItems = $feedItems;
			
			return $this;
		}
		
		/**
		 * @return FeedChannelExt
		**/
		public function addFeedItem(FeedItem $feedItem)
		{
			$this->feedItems[] = $feedItem;
			
			return $this;
		}
	}
?>