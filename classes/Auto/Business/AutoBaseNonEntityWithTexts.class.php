<?php
/*****************************************************************************
 *   Copyright (C) 2006-2009, onPHP's MetaConfiguration Builder.             *
 *   Generated by onPHP-1.1.master at 2014-07-22 19:02:51                    *
 *   This file is autogenerated - do not edit.                               *
 *****************************************************************************/

	abstract class AutoBaseNonEntityWithTexts extends BaseNonEntity
	{
		protected $textShort = null;
		protected $text = null;
		
		public function getTextShort()
		{
			return $this->textShort;
		}
		
		/**
		 * @return BaseNonEntityWithTexts
		**/
		public function setTextShort($textShort)
		{
			$this->textShort = $textShort;
			
			return $this;
		}
		
		public function getText()
		{
			return $this->text;
		}
		
		/**
		 * @return BaseNonEntityWithTexts
		**/
		public function setText($text)
		{
			$this->text = $text;
			
			return $this;
		}
	}
?>