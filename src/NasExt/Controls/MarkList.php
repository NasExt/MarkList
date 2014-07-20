<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Controls;

use NasExt\Controls\Storage\IMarkListStorage;
use NasExt\Controls\Storage\IMarkListStorageSection;
use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;
use Nette\ComponentModel\IComponent;

/**
 * MarkList
 *
 * @author Dusan Hudak
 */
class MarkList extends Control
{

	const MARK_ALL = 'MARK_ALL';
	const MARK_LIST = 'MARK_LIST';

	/** @var IMarkListStorage */
	private $storage;

	/** @var IMarkListStorageSection */
	private $storageSection;

	/** @var  bool */
	private $ajaxRequest;

	/** @var array */
	public $onMarkAll;

	/** @var array */
	public $onUnMarkAll;

	/** @var array */
	public $onMarkItem;

	/** @var array */
	public $onUnMarkItem;

	/** @var  array */
	public $onProcessItem;

	/** @var  array */
	public $onProcessAll;

	/** @var  string */
	public $templateFile;

	/** @var  string */
	private $sectionName;

	/** @var  bool */
	public $rememberMarkList = TRUE;

	/** @persistent */
	public $mlId;


	/**
	 * @param IMarkListStorage $storage
	 */
	public function __construct(IMarkListStorage $storage)
	{
		parent::__construct();

		$this->templateFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MarkList.latte';

		$this->storage = $storage;
	}


	/**
	 * @param IComponent $presenter
	 */
	protected function attached($presenter)
	{
		parent::attached($presenter);

		if ($presenter instanceof Presenter) {
			$this->sectionName = substr(md5($this->reflection->getShortName() . '.' . $this->name . '.' . $this->presenter->name), 0, 4);

			if ($this->rememberMarkList == FALSE) {
				if ($this->mlId != $this->sectionName) {
					$this->getStorageSection($this->sectionName)->deleteAll();
				}
			}

			$this->mlId = $this->sectionName;
		}
	}


	/**
	 * @return IMarkListStorageSection
	 */
	private function getStorageSection()
	{
		if (!$this->storageSection) {
			$this->storageSection = $this->storage->getSection($this->sectionName);
		}

		return $this->storageSection;
	}


	/**
	 * @param bool $value
	 */
	public function setAjaxRequest($value = TRUE)
	{
		$this->ajaxRequest = $value;
	}


	/**
	 * @return bool
	 */
	public function getMarkAll()
	{
		return $this->getStorageSection()->get(self::MARK_ALL);
	}


	/**
	 * @param bool $value
	 * @return MarkList provides fluent interface
	 */
	public function setMarkAll($value = TRUE)
	{
		$this->getStorageSection()->add(self::MARK_ALL, $value);
		return $this;
	}


	public function setUnmarkAll()
	{
		$this->getStorageSection()->delete(self::MARK_LIST);
		$this->setMarkAll(FALSE);
		$this->onUnMarkAll($this);
	}


	/**
	 * @param bool $asArrayList Return markList ids in array values
	 * @return bool|array
	 */
	public function getMarkList($asArrayList = FALSE)
	{
		$list = $this->getStorageSection()->get(self::MARK_LIST);

		if ($asArrayList == TRUE) {
			if ($list) {
				$list = array_keys($list);
			}
		}

		return $list;
	}


	/**
	 * @param array $markList
	 * @return array|bool
	 */
	public function createMarkList($markList)
	{
		$this->getStorageSection()->add(self::MARK_LIST, $markList);
		return $markList;
	}


	/**
	 * @param string $name
	 * @return bool| mixed of MarkList
	 */
	public function getItem($name)
	{
		$markList = $this->getMarkList();

		if ($markList && array_key_exists($name, $markList)) {
			return $markList[$name];
		}
		return FALSE;
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 * @return array|bool
	 */
	public function setItem($name, $value)
	{
		$markList = $this->getMarkList();

		if ($value == TRUE) {
			$markList[$name] = $value;
		} else {
			if (array_key_exists($name, $markList)) {
				unset($markList[$name]);
			}
		}

		return $this->createMarkList($markList);
	}


	/**
	 * HANDLE - Process All
	 */
	public function handleProcessAll()
	{
		$markList = $this->getMarkList();
		if ($markList) {
			$this->setUnmarkAll();
		} else {
			if ($this->getMarkAll() == FALSE) {
				$this->setMarkAll(TRUE);
				$this->onMarkAll($this);
			} else {
				$this->getStorageSection()->delete(self::MARK_LIST);
				$this->setMarkAll(FALSE);
				$this->onUnMarkAll($this);
			}
		}

		$this->onProcessAll($this);

		if (!$this->presenter->isAjax()) {
			$this->presenter->redirect('this');
		}
	}


	/**
	 * HANDLE - Process Item
	 * @param string $name
	 */
	public function handleProcessItem($name)
	{
		$item = $this->getItem($name);

		if ($item == FALSE) {
			$this->setItem($name, TRUE);
			$this->onMarkItem($this);
		} else {
			$this->setItem($name, FALSE);
			$this->onUnMarkItem($this);
		}

		$this->onProcessItem($this);

		if (!$this->presenter->isAjax()) {
			$this->presenter->redirect('this');
		}
	}


	/**
	 * @param bool|mixed $item
	 */
	public function render($item = FALSE)
	{
		$template = $this->template;

		$template->item = $item;
		$template->itemValue = $item ? $this->getItem($item) : FALSE;
		$template->markAll = $this->getMarkAll();
		$template->ajaxRequest = $this->ajaxRequest;
		$template->markList = $this->getMarkList();

		$template->setFile($this->templateFile);
		$template->render();
	}
}
