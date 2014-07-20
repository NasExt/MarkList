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

/**
 * IMarkListStorageSection
 *
 * @author Dusan Hudak
 */
interface IMarkListStorageSection
{

	/**
	 * @param int $name
	 * @param mixed $value
	 * @return array
	 */
	public function add($name, $value = TRUE);


	/**
	 * @param int $name
	 * @param mixed $value
	 * @return array
	 */
	public function edit($name, $value = TRUE);


	/**
	 * @param $name
	 * @return mixed
	 */
	public function get($name);


	/**
	 * @return array
	 */
	public function getAll();


	/**
	 * @param int $name
	 * @return array
	 */
	public function delete($name);


	/**
	 * @return array
	 */
	public function deleteAll();
}
