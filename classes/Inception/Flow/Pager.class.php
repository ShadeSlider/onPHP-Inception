<?php
/**
 * @author Alexander S. Evdokimov <aie@rdw.ru>, Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 * @copyright 2005-2014 Alexander S. Evdokimov <aie@rdw.ru>, Eric I. Gorbikov <ernest.gorbikov@gmail.com>
 */
if (!defined('EXT_PAGER'))
	define('EXT_PAGER', '.tpl.html'); //расширения шаблонов Pager'a

class Pager
{
	const FIRST_ELEMENT = 1;

	protected $handler 			    = null; // класс с логикой

	protected $area				    = null; // Название модуля

	protected $deltaPageNumber	    = null;	// Количество страниц до многоточия
	protected $totalPage		    = null;	// Всего страниц
	protected $totalElement		    = null;	// Кол-во элементов всего, на всех страницах.
	protected $currentPage		    = 1; 	// Текущая страница, 1-я по умолчанию.
	protected $elementPerPage   	= null;	// Кол-во элементов на странице
	protected $urlPrefix		    = 'start'; // Префикс, который будет использоваться при формировании ссылок на другие страницы пэйджера
	protected $urlParameters	    = array('action' => 'filter');
	protected $extraUrlParameters   = array();
	protected $onClick			    = '';
	protected $router			    = null;

	/**
	 * @return Pager
	 */
	public static function create($elementPerPage, $handler = "PagerDefault", $deltaPageNumber = 10)
	{
		return new self($elementPerPage, $handler, $deltaPageNumber);
	}

	/**
	 * @param integer $elementPerPage  кол-во элементов на странице
	 * @param integer $deltaPageNumber количество страниц до многоточия
	 * @param string $handler название класса с логикой
	 */
	public function __construct($elementPerPage, $handler = "PagerDefault", $deltaPageNumber = 10)
	{
		$this->elementPerPage = (int) $elementPerPage;
		$this->deltaPageNumber = (int) $deltaPageNumber;

		if (class_exists($handler)) {
            $this->handler = new $handler($this);
        } else {
            $this->handler = new PagerDefault($this);
        }
	}

	public function setRouter($router)
	{
		$this->router = $router;

		return $this;
	}

	public function getRouter()
	{
		return $this->router;
	}

	public function getUrlPrefix()
	{
		return $this->urlPrefix;
	}

	public function setUrlPrefix($urlPrefix)
	{
		$this->urlPrefix = $urlPrefix;

		return $this;
	}

	/**
	 * Всего страниц
	 *
	 * @return integer
	 */
	public function getTotalPage()
	{
		return $this->totalPage;
	}

	/**
	 * Число элементов на странице
	 *
	 * @return integer
	 */
	public function getElementPerPage()
	{
		return $this->elementPerPage;
	}

	/**
	 * Всего элементов
	 *
	 * @see Pager::setTotalElement()
	 * @return integer
	 */
	public function getTotalElement()
	{
		return $this->totalElement;
	}

	/**
	 * Общее количество элементов
	 *
	 * @param integer $totalElement
	 * @return Pager
	 */
	public function setTotalElement($totalElement)
	{
		$totalElement = (int) $totalElement;
		$this->totalElement = $totalElement;
		$this->setTotalPage($totalElement);

		/**
		 * @todo
		 */
		if ($this->handler instanceof PagerDefault)
			$this->handler->setTotalInterval($totalElement);

		return $this;
	}

	/**
	 * Возвращает текущюю страницу
	 *
	 * @see Pager::setCurrentPage()
	 * @return integer
	 */
	public function getCurrentPage()
	{
		return $this->currentPage;
	}

	/**
	 * Определяем номер текущей страницы.
	 *
	 * @param string $startElement номер первого элемента на странице (смещение от начала всей выборки)
	 * @return Pager
	 */
	public function setCurrentPage($startElement)
	{
		$startElement = (int) $startElement;
		$this->currentPage = 1 + (int) ($startElement / $this->elementPerPage);

		/**
		 * @todo
		 */
		if ($this->handler instanceof PagerDefault)
			$this->handler->setCurrentInterval($startElement);

		return $this;
	}

	/**
	 * Возвращает строку, приписваемую событию onclick
	 *
	 */
	public function getOnClick()
	{
		return $this->onClick;
	}

	/**
	 * Задает строку, приписваемую событию onclick
	 *
	 */
	public function setOnClick($onClick)
	{
		$this->onClick = $onClick;
		return $this;
	}

	/**
	 * Количество страниц в интервале
	 *
	 * @return integer
	 */
	public function getDeltaPage()
	{
		return $this->deltaPageNumber;
	}

	/**
	 * Устанавливает дополнительные параметры ссылки
	 * При использовании этого методы вы должны позаботиться, что передаете в него чистые, обработанные параметры
	 *
	 * @param array $params
	 * @return Pager
	 */
	public function setUrlParameters($params)
	{
		$this->urlParameters = $params;

		return $this;
	}

	/**
	 * Сброс параметров url
	 *
	 * @return Pager
	 */
	public function resetUrlParameters()
	{
		$this->urlParameters = array();

		return $this;
	}

	/**
	 * Возвращает дополнительные параметры ссылки
	 *
	 * @return array
	 */
	public function getUrlParameters()
	{
		return $this->urlParameters;
	}


	/**
	 * @return array
	 */
	public function getExtraUrlParameters()
	{
		return $this->extraUrlParameters;
	}

	/**
	 * @return static
	 */
	public function setExtraUrlParameters($extraUrlParameters)
	{
		$this->extraUrlParameters = $extraUrlParameters;

		return $this;
	}


	/**
	 * @return $this
	 */
	public function resetExtraUrlParameters()
	{
		$this->extraUrlParameters = array();

		return $this;
	}


	/**
	 * Подготовка href для ссылки
	 *
	 * @param integer $startElement
	 */
	public function getHrefByStartElement($startElement)
	{
		$base = $this->getBaseUrl();

		$delimiter =
			(strpos($base, '?') === false)
				? '?'
				: '&';

		$this->urlParameters[$this->urlPrefix] = (int) $startElement;

		if ($name = $this->getRouter()) {
			$url = RouterUrlHelper::url($this->urlParameters, $name, true);
			$extraUrlParamString = '';

			if(count($this->extraUrlParameters)) {
				$extraUrlParamString = $this->getQueryStringByParametrs($this->extraUrlParameters, '?');
			}

			return $url . $extraUrlParamString;
		}
		else
			return
				$base
				.$delimiter
				.$this->toStingParameters();
	}

	public function dump($prefix = 'default')
	{
		/**
		 * Если страница одна - то педжер отображаться не должен (как в яндексе).
		 * Иначе выглядит нелепо
		 */
		if ($this->getTotalPage() < 2) {
			return $this;
		}
		require DIR_TEMPLATES.'pager'.DIRECTORY_SEPARATOR.$prefix.".".get_class($this->handler).EXT_PAGER;

		return $this;
	}

	/************* Protected Methods *********************/

	/**
	 * Расчет требуемого количества страниц для отображения $totalElement
	 *
	 * @param string $totalElement
	 * @return integer
	 */
	protected function setTotalPage($totalElement)
	{
		$this->totalPage =
			($totalElement)
				? ceil($totalElement/$this->elementPerPage)
				: self::FIRST_ELEMENT;

		return $this;
	}

	/**
	 * Базовый Url
	 *
	 * @see Pager::gerBaseHref()
	 * @return string
	 */
	protected function getBaseUrl()
	{
		return PATH_WEB.basename(
			strip_tags($_SERVER['PHP_SELF'])
		);
	}

	/**
	 * Параметры, которые надо добавить к Url
	 *
	 * @return string
	 */
	protected function toStingParameters()
	{
		return self::getQueryStringByParametrs($this->urlParameters, '');
	}

	final public static function getQueryStringByParametrs($arrayParams = array(), $prefix = '&', $separator = '&')
	{
		Assert::isArray($arrayParams);

		$arrayParams = array_filter($arrayParams);
		if (count($arrayParams)) {
			return
				str_replace(
					'+',
					'%20',
					$prefix.http_build_query($arrayParams, '', $separator)
				);
		}

		return '';
	}
}