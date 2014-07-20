<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Controls\Storage;

use Nette\Http\SessionSection;

/**
 * MarkListStorageSection
 *
 * @author Dusan Hudak
 */
class MarkListStorageSection implements IMarkListStorageSection
{

	/** @var  SessionSection */
	private $section;


	/**
	 * @param SessionSection $section
	 */
	public function __construct(SessionSection $section)
	{
		$this->section = $section;
	}


	/**
	 * @param int $name
	 * @param mixed $value
	 * @return array
	 */
	public function add($name, $value = TRUE)
	{
		$this->section[$name] = $value;
		return $this->getAll();
	}


	/**
	 * @param int $name
	 * @param mixed $value
	 * @return array
	 */
	public function edit($name, $value = TRUE)
	{
		$this->section[$name] = $value;
		return $this->getAll();
	}


	/**
	 * @param $name
	 * @return mixed
	 */
	public function get($name)
	{
		return $this->section[$name];
	}


	/**
	 * @return array
	 */
	public function getAll()
	{
		return $this->section->getIterator()->getArrayCopy();
	}


	/**
	 * @param int $name
	 * @return array
	 */
	public function delete($name)
	{
		unset($this->section[$name]);
		return $this->getAll();
	}


	/**
	 * @return array
	 */
	public function deleteAll()
	{
		$this->section->remove();
		return $this->getAll();
	}
}
