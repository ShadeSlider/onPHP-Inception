<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-05-16 20:33:06                    *
 *   This file will never be generated again - feel free to edit.            *
 *****************************************************************************/

class BaseEntity extends AutoBaseEntity implements Prototyped
{
	/**
	 * @return BaseEntity
	**/
	public static function create()
	{
		return new self;
	}


	/**
	 * @return ProtoBaseEntity
	**/
	public static function proto()
	{
		return Singleton::getInstance('ProtoBaseEntity');
	}

	// your brilliant stuff goes here
}