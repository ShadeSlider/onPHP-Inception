<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-07-23 08:21:16                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	abstract class AutoEmployeeDAO extends BaseEntityWithTextsDAO
	{
		public function getTable()
		{
			return 'employee';
		}
		
		public function getObjectName()
		{
			return 'Employee';
		}
		
		public function getSequence()
		{
			return 'employee_id_seq';
		}
	}
?>