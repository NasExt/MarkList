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

use Nette\Http\Session;
use Nette\Object;
use Nette\Utils\Strings;


/**
 * MarkListStorage
 *
 * @author Dusan Hudak
 */
class MarkListStorage extends Object implements IMarkListStorage
{
	/** @var  Session */
	private $session;


	/**
	 * @param Session $session
	 */
	public function __construct(Session $session)
	{
		$this->session = $session;
	}


	/**
	 * @param string $name
	 * @return IMarkListStorageSection
	 */
	public function getSection($name)
	{
		$section = $this->session->getSection(Strings::webalize($name));
		return new MarkListStorageSection($section);
	}
}
