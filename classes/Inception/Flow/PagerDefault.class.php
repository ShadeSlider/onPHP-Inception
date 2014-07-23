<?php
/***************************************************************************
 *   Copyright (C) 2007 by Diakonov Sergey                                 *
 *   sadya@rdw.ru                                                          *
 ***************************************************************************/
/* $Id$ */
	if (!defined('EXT_PAGER'))
		define('EXT_PAGER', '.tpl.html'); //расширения шаблонов Pager'a

	class PagerDefault
	{
		/**
		 * @var Pager
		 */
		protected $pager 	= null; // Класс Pager

		protected $totalInterval	= null;	// Количество интеравлов по $deltaPageNumber, необходимых для отображдения $totalPage
		protected $currentInterval	= null;	// Текущий интервал на котором находится страница

		public function __construct(Pager $pager)
		{
			$this->pager = $pager;
		}

		/**
		 * Номер текущего интервала
		 *
		 * @return integer
		 */
		public function getCurrentInterval()
		{
			return $this->currentInterval;
		}

		/**
		 * Определяет начальную страницу текущего интервала
		 *
		 * @return integer
		 */
		public function getBeginPageInterval()
		{
			if ($this->pager->getCurrentPage() <= ceil($this->pager->getDeltaPage()/2)) {
				return 1;

			} else {
				return (
					$this->pager->getCurrentPage() - ceil($this->pager->getDeltaPage()/2)
				);
			}

		}

		/**
		 * Определяет последнюю страницу интервала
		 *
		 * @return integer
		 */
		public function getEndPageInterval()
		{
			if ($this->pager->getCurrentPage() <= ceil($this->pager->getDeltaPage()/2)) {
				return $this->pager->getDeltaPage();
			} else {
				return (
					$this->pager->getCurrentPage() + ceil($this->pager->getDeltaPage()/2)
				);
			}

		}

		/**
		 * Число интервалов
		 *
		 * @return integer
		 */
		public function getTotalInterval()
		{
			return $this->totalInterval;
		}

		/**
		 * Определение текущего интервала
		 *
		 * @param integer $startElement
		 * @return Pager
		 */
		public function setCurrentInterval($startElement)
		{
			if ($startElement != 0)
				$this->currentInterval = ceil(($startElement+1)/($this->pager->getDeltaPage() * $this->pager->getElementPerPage()));
			else
				$this->currentInterval = Pager::FIRST_ELEMENT;

			return $this;
		}

		/**
		 * Расчет требуемого количества интервалов
		 *
		 * @param integer $totalElement
		 * @return integer
		 */
		public function setTotalInterval($totalElement)
		{
			$this->totalInterval = ($totalElement)
				? ceil($totalElement/($this->pager->getDeltaPage() * $this->pager->getElementPerPage()))
				: Pager::FIRST_ELEMENT;

			return $this;
		}
	}
?>
