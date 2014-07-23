<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-07-23 08:21:16                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	abstract class AutoProtoEmployee extends ProtoBaseEntityWithTexts
	{
		protected function makePropertyList()
		{
			return
				array_merge(
					parent::makePropertyList(),
					array(
						'email' => LightMetaProperty::fill(new LightMetaProperty(), 'email', null, 'string', null, 255, false, true, false, null, null),
						'firstName' => LightMetaProperty::fill(new LightMetaProperty(), 'firstName', 'first_name', 'string', null, 255, true, true, false, null, null),
						'lastName' => LightMetaProperty::fill(new LightMetaProperty(), 'lastName', 'last_name', 'string', null, 255, true, true, false, null, null),
						'middleName' => LightMetaProperty::fill(new LightMetaProperty(), 'middleName', 'middle_name', 'string', null, 255, false, true, false, null, null),
						'mobilePhone' => LightMetaProperty::fill(new LightMetaProperty(), 'mobilePhone', 'mobile_phone', 'string', null, 255, false, true, false, null, null),
						'workPhone' => LightMetaProperty::fill(new LightMetaProperty(), 'workPhone', 'work_phone', 'string', null, 255, false, true, false, null, null),
						'gender' => LightMetaProperty::fill(new LightMetaProperty(), 'gender', null, 'string', null, 1, false, true, false, null, null),
						'position' => LightMetaProperty::fill(new LightMetaProperty(), 'position', null, 'string', null, 255, false, true, false, null, null),
						'birthDate' => LightMetaProperty::fill(new LightMetaProperty(), 'birthDate', 'birth_date', 'date', 'Date', null, false, true, false, null, null),
						'id' => LightMetaProperty::fill(new LightMetaProperty(), 'id', null, 'integerIdentifier', 'Employee', 4, true, true, false, null, null)
					)
				);
		}
	}
?>