<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-07-22 19:02:51                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	abstract class AutoProtoBaseNonEntity extends ProtoBaseStorable
	{
		protected function makePropertyList()
		{
			return
				array_merge(
					parent::makePropertyList(),
					array(
						'createdAt' => LightMetaProperty::fill(new LightMetaProperty(), 'createdAt', 'created_at', 'timestampTZ', 'TimestampTZ', null, false, true, false, null, null),
						'updatedAt' => LightMetaProperty::fill(new LightMetaProperty(), 'updatedAt', 'updated_at', 'timestampTZ', 'TimestampTZ', null, false, true, false, null, null),
						'deletedAt' => LightMetaProperty::fill(new LightMetaProperty(), 'deletedAt', 'deleted_at', 'timestampTZ', 'TimestampTZ', null, false, true, false, null, null),
						'id' => LightMetaProperty::fill(new LightMetaProperty(), 'id', null, 'integerIdentifier', 'BaseNonEntity', 4, true, true, false, null, null)
					)
				);
		}
	}
?>