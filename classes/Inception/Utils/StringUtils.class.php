<?php
/**
 * @author Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2014 Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */

class StringUtils extends StaticFactory {


	/**
	 * @return string
	 */
	public static function camelCaseToDashed($str)
	{
		$lcStr = lcfirst($str);


		preg_match('/([a-z0-9]+)(((?:[A-Z][a-z0-9]+)|(?:[A-Z]+))+)$/', $lcStr, $matches);

		if(empty($matches[1]) || empty($matches[2])) {
			return $lcStr;
		}

		$outStr = $matches[1];
		preg_match_all('/(([A-Z][a-z0-9]+)|([A-Z0-9]+)|([a-z0-9])+)+?/', $matches[2], $matchesInner);

		foreach ($matchesInner[0] as $word) {
			$outStr .= '-' . strtolower($word);
		}

		return $outStr;
	}


	/**
	 * @return string
	 */
	public static function dashedToCamelCase($str, $pascalCase = false)
	{
		if($pascalCase) {
			$ucStr = ucfirst($str);
		}
		else {
			$ucStr = $str;
		}

		preg_match('/([a-zA-Z0-9]+?)((-[a-z0-9]+)+)$/', $ucStr, $matches);


		if(empty($matches[1]) || empty($matches[2])) {
			return $ucStr;
		}

		$outStr = $matches[1];
		preg_match_all('/(-)([a-zA-Z0-9]+)+?/', $matches[2], $matchesInner);

		foreach ($matchesInner[2] as $word) {
			$outStr .= ucfirst($word);
		}

		return $outStr;
	}


	/**
	 * @return string
	 */
	public static function formatPrice($price, $decimals = 2, $decPoint = ',', $thousandsSep = ' ')
	{
		return number_format($price, $decimals, $decPoint, $thousandsSep);
	}


	/**
	 * @return mixed
	 */
	public static function transliterate($string, $removePunctuation = false, $reverseMode = false, $lowerCase = false, $replaces = array())
	{
		$table = array(
			'А' => 'A',
			'Б' => 'B',
			'В' => 'V',
			'Г' => 'G',
			'Д' => 'D',
			'Е' => 'E',
			'Ё' => 'YO',
			'Ж' => 'ZH',
			'З' => 'Z',
			'И' => 'I',
			'Й' => 'J',
			'К' => 'K',
			'Л' => 'L',
			'М' => 'M',
			'Н' => 'N',
			'О' => 'O',
			'П' => 'P',
			'Р' => 'R',
			'С' => 'S',
			'Т' => 'T',
			'У' => 'U',
			'Ф' => 'F',
			'Х' => 'H',
			'Ц' => 'C',
			'Ч' => 'CH',
			'Ш' => 'SH',
			'Щ' => 'CSH',
			'Ь' => '',
			'Ы' => 'Y',
			'Ъ' => '',
			'Э' => 'E',
			'Ю' => 'YU',
			'Я' => 'YA',

			'а' => 'a',
			'б' => 'b',
			'в' => 'v',
			'г' => 'g',
			'д' => 'd',
			'е' => 'e',
			'ё' => 'yo',
			'ж' => 'zh',
			'з' => 'z',
			'и' => 'i',
			'й' => 'j',
			'к' => 'k',
			'л' => 'l',
			'м' => 'm',
			'н' => 'n',
			'о' => 'o',
			'п' => 'p',
			'р' => 'r',
			'с' => 's',
			'т' => 't',
			'у' => 'u',
			'ф' => 'f',
			'х' => 'h',
			'ц' => 'c',
			'ч' => 'ch',
			'ш' => 'sh',
			'щ' => 'csh',
			'ь' => '',
			'ы' => 'y',
			'ъ' => '',
			'э' => 'e',
			'ю' => 'yu',
			'я' => 'ya',
			' ' => '-',
			'---' => '-',
			'--' => '-'
		);

		foreach ($replaces as $k => $v) {
			$table[$k] = $v;
		}
		if ($reverseMode) {
			$table = array_flip($table);
		}
		$output = str_replace(
			array_values($replaces),
			array_keys($replaces), $string
		);
		$output = str_replace(
			array_keys($table),
			array_values($table), $output
		);

		if ($removePunctuation && !$reverseMode) {
			$output = preg_replace('|[^0-9a-zA-z\-_]|', '', $output);
		}
		return $output;
	}


	/**
	 * @return string
	 */
	public static function makeRandomString($length = 10, $letters = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM')
	{
		$s = '';
		$lettersLength = strlen($letters) - 1;

		for ($i = 0; $i < $length; $i++) {
			$s .= $letters[rand(0, $lettersLength)];
		}

		return $s;
	}


	/**
	 * Generate an array of word tails for a russian word
	 * @return string
	 */
	public static function getWordTail($number, array $variants)
	{
		$number = (int)$number;

		if (count($variants) < 3)
			return '';

		if ($number <= 0)
			return $variants[0];

		if (preg_match('/1\d$/', $number))
			return $variants[0];
		elseif (preg_match('/1$/', $number))
			return $variants[1];
		elseif (preg_match('/(2|3|4)$/', $number))
			return $variants[2];
		else
			return $variants[0];

	}


	/**
	 * @return string
	 */
	public static function htmlEntityDecodeExt($string)
	{
		$string = str_replace('&raquo;', '»', $string);
		$string = str_replace('&laquo;', '«', $string);
		$string = str_replace('&sbquo;', ',', $string);
		$string = html_entity_decode($string);
		return $string;
	}


	/**
	 * @return string
	 */
	public static function getClippedUrlString($string, $length = 50, $filler = "...")
	{
		$strlen = strlen($string);

		if ($strlen <= $length) {
			return $string;
		}

		$firstHalf = substr($string, 0, $length / 2);
		$secondHalf = substr($string, -($length / 2));

		return $firstHalf . $filler . $secondHalf;
	}
}