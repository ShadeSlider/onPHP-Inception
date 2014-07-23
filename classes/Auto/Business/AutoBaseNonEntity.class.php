<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-07-22 19:02:51                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	abstract class AutoBaseNonEntity extends BaseStorable
	{
		protected $createdAt = null;
		protected $updatedAt = null;
		protected $deletedAt = null;
		
		/**
		 * @return TimestampTZ
		**/
		public function getCreatedAt()
		{
			return $this->createdAt;
		}
		
		/**
		 * @return BaseNonEntity
		**/
		public function setCreatedAt(TimestampTZ $createdAt = null)
		{
			$this->createdAt = $createdAt;
			
			return $this;
		}
		
		/**
		 * @return BaseNonEntity
		**/
		public function dropCreatedAt()
		{
			$this->createdAt = null;
			
			return $this;
		}
		
		/**
		 * @return TimestampTZ
		**/
		public function getUpdatedAt()
		{
			return $this->updatedAt;
		}
		
		/**
		 * @return BaseNonEntity
		**/
		public function setUpdatedAt(TimestampTZ $updatedAt = null)
		{
			$this->updatedAt = $updatedAt;
			
			return $this;
		}
		
		/**
		 * @return BaseNonEntity
		**/
		public function dropUpdatedAt()
		{
			$this->updatedAt = null;
			
			return $this;
		}
		
		/**
		 * @return TimestampTZ
		**/
		public function getDeletedAt()
		{
			return $this->deletedAt;
		}
		
		/**
		 * @return BaseNonEntity
		**/
		public function setDeletedAt(TimestampTZ $deletedAt = null)
		{
			$this->deletedAt = $deletedAt;
			
			return $this;
		}
		
		/**
		 * @return BaseNonEntity
		**/
		public function dropDeletedAt()
		{
			$this->deletedAt = null;
			
			return $this;
		}
	}
?>