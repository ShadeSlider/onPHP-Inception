<?php
/***************************************************************************
 *   Copyright (C) 2008 by Sergey Gorelov                                  *
 *   sgorelov@fjproject.ru                                                 *
 ***************************************************************************/
/* $Id: TimestampRussianUtils.class.php 2948 2008-11-01 11:00:35Z volerog $ */

	/**
	 * Утилиты для вывода времени на русском.
	 *
	 */
	class TimestampRussianUtils
	{
		public static $MONTH_TRANSLATION = array(
			'01' => 'января',
			'02' => 'февраля',
			'03' => 'марта',
			'04' => 'апреля',
			'05' => 'мая',
			'06' => 'июня',
			'07' => 'июля',
			'08' => 'августа',
			'09' => 'сентябя',
			'10' => 'октября',
			'11' => 'ноября',
			'12' => 'декабря'
			);

		/**
		 * Выводит дату на русском.
		 *
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getTextDate(Timestamp $date)
		{
			$now = Timestamp::create('now');
			if (Timestamp::dayDifference($now, $date) == 0)
				$daystr = 'сегодня';
			elseif (Timestamp::dayDifference($now, $date) == -1)
				$daystr = 'вчера';
			else
				$daystr = intval($date->getDay()).' '.self::$MONTH_TRANSLATION[$date->getMonth()].' '.$date->getYear();

			return $daystr;
		}

		/**
		 * Выводит дату числами, но в правильном порядке
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getDigitDate(Date $date)
		{
			return $date->getDay().'.'.$date->getMonth().'.'.$date->getYear();
		}

		/**
		 * Выводит дату числами через Слэш и в правильном порядке
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getSlashDate(Date $date)
		{
			return $date->getDay().'/'.$date->getMonth().'/'.$date->getYear();
		}

		/**
		 * Выводит время для контента
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getContentTime(Timestamp $date)
		{
			$now = Timestamp::create('now');
			if (Timestamp::dayDifference($now, $date) == 0)
				$daystr='сегодня, '.$date->getHour().':'.$date->getMinute();
			elseif (Timestamp::dayDifference($now, $date) == -1)
				$daystr = 'вчера, '.$date->getHour().':'.$date->getMinute();
			else
				$daystr = self::getDigitDate($date);

			return $daystr;
		}

		/**
		 * Выводит время для комментариев
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getCommentTime(Timestamp $date)
		{
			$now = Timestamp::create('now');
			if (Timestamp::dayDifference($now, $date) == 0)
				$daystr = 'сегодня, '.$date->getHour().':'.$date->getMinute();
			elseif (Timestamp::dayDifference($now, $date) == -1)
				$daystr = 'вчера, '.$date->getHour().':'.$date->getMinute();
			else
				$daystr = self::getDigitDate($date).', '.$date->getHour().':'.$date->getMinute();

			return $daystr;
		}

		/**
		 * Выводит время с датой числами, но в правильном порядке
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getDigitTime(Timestamp $date)
		{
			return self::getDigitDate($date).' '.$date->getHour().':'.$date->getMinute();
		}

		/**
		 * Выводит время с датой числами, но в правильном порядке
		 * @param Timestamp $date
		 * @return string
		 */
		public static function getTextTime(Timestamp $date)
		{
			return self::getTextDate($date).' '.$date->getHour().':'.$date->getMinute();
		}

		/**
		 * Выводит длительность заданную в секундах человекочитаемой строкой
		 * @param integer $duration
		 * @return string
		 */
		public static function getDurationTime($duration)
		{
			if ($duration<0) return 'отрицательное время';

			$res=($duration%60).'&nbspсек.';
			if ($duration<60) return $res;

			$res=(floor($duration/60) % 60).'&nbspмин. '.$res;
			if ($duration<60*60)  return $res;

			$res=(floor($duration/60/60)).'&nbspч. '.$res;
			return $res;
		}

		/**
		 * Выводит длительность заданную в секундах человекочитаемой строкой
		 * @param integer $duration
		 * @return string
		 */
		public static function getDurationTimeMMSS($duration)
		{
			if ($duration<0) return 'отрицательное время';

			$res=($duration%60).'&nbspсек.';
			if ($duration<60) return $res;

			$res=(floor($duration/60)).'&nbspмин. '.$res;
			return $res;
		}

		/**
		 * Выводит длительность заданную в секундах человекочитаемой строкой
		 * @param integer $duration
		 * @return string
		 */
		public static function getDurationTimeDDHH($duration)
		{
			if ($duration<0) return 'отрицательное время';

			$hhdd = $duration / 60 / 60;

			$d = $hhdd % 24;
			$res = $d.'&nbsp'.RussianTextUtils::selectCaseForNumber($d, array('час','часа','часов')).' ';
			if ($hhdd < 24) return $res;

			$d = floor($hhdd/24);
			$res = $d.'&nbsp'.RussianTextUtils::selectCaseForNumber($d, array('день','дня','дней')).' '.$res;
			return $res;
		}

		public static function getActivityTime(Timestamp $date)
		{
			$now = Timestamp::create('now');
			if (Timestamp::dayDifference($now, $date) == 0)
				$daystr = $date->getHour().':'.$date->getMinute();
			elseif (Timestamp::dayDifference($now, $date) == -1)
				$daystr = 'вчера '.$date->getHour().':'.$date->getMinute();
			else
				$daystr = intval($date->getDay()).' '.self::$MONTH_TRANSLATION[$date->getMonth()].' '.$date->getYear();

			return $daystr;

		}

		public static function utfWrap($str, $width = 7)
		{
			return mb_strlen($str) > $width ? mb_substr($str, 0, $width).'...' : $str;
		}
	}
?>
