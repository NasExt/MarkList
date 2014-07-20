<?php

/**
 * This file is part of the NasExt extensions of Nette Framework
 *
 * Copyright (c) 2013 Dusan Hudak (http://dusan-hudak.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace NasExt\Controls\DI;

use Nette\Configurator;
use Nette\DI\Compiler;
use Nette\DI\CompilerExtension;

if (!class_exists('Nette\DI\CompilerExtension')) {
	class_alias('Nette\Config\CompilerExtension', 'Nette\DI\CompilerExtension');
	class_alias('Nette\Config\Compiler', 'Nette\DI\Compiler');
}

if (isset(\Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']) || !class_exists('Nette\Configurator')) {
	unset(\Nette\Loaders\NetteLoader::getInstance()->renamed['Nette\Configurator']);
	class_alias('Nette\Config\Configurator', 'Nette\Configurator');
}

/**
 * MarkListExtension
 *
 * @author Dusan Hudak
 */
class MarkListExtension extends CompilerExtension
{

	/** @var array */
	public $defaults = array(
		'ajaxRequest' => TRUE,
		'storage' => FALSE,
		'rememberMarkList' => FALSE
	);


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$storage = $config['storage'];
		if ($storage == FALSE) {
			$builder->addDefinition($this->prefix('storage'))
				->setClass('NasExt\Controls\Storage\MarkListStorage')
				->setAutowired(FALSE);

			$storage = $this->prefix('@storage');
		}

		$builder->addDefinition($this->prefix('markListFactory'))
			->setImplement('\NasExt\Controls\IMarkListFactory')
			->setFactory('NasExt\Controls\MarkList', array($storage))
			->addSetup('setAjaxRequest', array($config['ajaxRequest']))
			->addSetup('$rememberMarkList', array($config['rememberMarkList']));
	}


	/**
	 * @param Configurator $configurator
	 */
	public static function register(Configurator $configurator)
	{
		$configurator->onCompile[] = function (Configurator $config, Compiler $compiler) {
			$compiler->addExtension('markList', new MarkListExtension());
		};
	}
}
