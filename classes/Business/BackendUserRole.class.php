<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-04-11 12:53:24                    *
 *   This file will never be generated again - feel free to edit.            *
 *****************************************************************************/

	class BackendUserRole extends AutoBackendUserRole implements Prototyped, DAOConnected
	{
		/**
		 * @return BackendUserRole
		**/
		public static function create()
		{
			return new self;
		}
		
		/**
		 * @return BackendUserRoleDAO
		**/
		public static function dao()
		{
			return Singleton::getInstance('BackendUserRoleDAO');
		}
		
		/**
		 * @return ProtoBackendUserRole
		**/
		public static function proto()
		{
			return Singleton::getInstance('ProtoBackendUserRole');
		}
		
		// your brilliant stuff goes here
	}
?>