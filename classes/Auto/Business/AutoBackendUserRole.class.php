<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-07-22 19:02:51                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	abstract class AutoBackendUserRole extends BaseEntity
	{
		protected $parent = null;
		protected $name = null;
		protected $title = '';
		protected $permissions = null;
		
		/**
		 * @return BackendUserRole
		**/
		public function getParent()
		{
			return $this->parent;
		}
		
		/**
		 * @return BackendUserRole
		**/
		public function setParent(BackendUserRole $parent = null)
		{
			$this->parent = $parent;
			
			return $this;
		}
		
		/**
		 * @return BackendUserRole
		**/
		public function dropParent()
		{
			$this->parent = null;
			
			return $this;
		}
		
		public function getName()
		{
			return $this->name;
		}
		
		/**
		 * @return BackendUserRole
		**/
		public function setName($name)
		{
			$this->name = $name;
			
			return $this;
		}
		
		public function getTitle()
		{
			return $this->title;
		}
		
		/**
		 * @return BackendUserRole
		**/
		public function setTitle($title)
		{
			$this->title = $title;
			
			return $this;
		}
		
		/**
		 * @return BackendUserRolePermissionsDAO
		**/
		public function getPermissions($lazy = false)
		{
			if (!$this->permissions || ($this->permissions->isLazy() != $lazy)) {
				$this->permissions = new BackendUserRolePermissionsDAO($this, $lazy);
			}
			
			return $this->permissions;
		}
		
		/**
		 * @return BackendUserRole
		**/
		public function fillPermissions($collection, $lazy = false)
		{
			$this->permissions = new BackendUserRolePermissionsDAO($this, $lazy);
			
			if (!$this->id) {
				throw new WrongStateException(
					'i do not know which object i belong to'
				);
			}
			
			$this->permissions->mergeList($collection);
			
			return $this;
		}
	}
?>