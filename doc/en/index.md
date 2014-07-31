NasExt/MarkList
===========================

MarkList for Nette Framework.

Requirements
------------

NasExt/MarkList requires PHP 5.3.2 or higher.

- [Nette Framework](https://github.com/nette/nette)

Installation
------------

The best way to install NasExt/MarkList is using  [Composer](http://getcomposer.org/):

```sh
$ composer require nasext/mark-list
```

Enable the extension using your neon config.

```yml
extensions:
	nasext.markList: NasExt\Controls\DI\MarkListExtension
```

Configuration
```yml
nasext.markList:
	ajaxRequest: TRUE
	storage => FALSE,
	rememberMarkList => FALSE
```
- ajaxRequest: (use with ajax)
- storage: (default storage is session, but when you need change storage there is way how to register IMarkListStorage)
- rememberMarkList: (remember when go to out from site)

## Usage
Inject \NasExt\Controls\IMarkListFactory in to presenter

````php
FooPresenter extends Presenter{

	/** @var  \NasExt\Controls\IMarkListFactory */
	private $markListFactory;

	/**
	 * INJECT MarkListFactory
	 * @param \NasExt\Controls\IMarkListFactory $markListFactory
	 */
	public function injectMarkListFactory(\NasExt\Controls\IMarkListFactory $markListFactory)
	{
		$this->markListFactory = $markListFactory;
	}

	/**
	 * RENDER - Default
	 */
	public function renderDefault()
	{

		// Component initialize
		$this->getComponent('markList');

		$repositoryData = $that->repository->findAll();
		$this->template->repositoryData = $repositoryData;
	}

	public function handleFoo()
	{
		/** @var MarkList $markList */
		$markList = $this->getComponent('markList');
		$markListData = $markList->getMarkList(TRUE);
	}

	/**
	 * CONTROL - MarkList
	 * @return \NasExt\Controls\MarkList
	 */
	protected function createComponentMarkList()
	{
		$that = $this;
		$control = $this->markListFactory->create();

		$invalidateControl = function () use ($that) {
			if ($that->isAjax()) {
				$that->redrawControl('table');
			}
		};

		$control->onProcessAll[] = $invalidateControl;
		$control->onProcessItem[] = $invalidateControl;

		$control->onMarkAll[] = function (MarkList $markList) use ($that) {
			$repositoryData = $that->repository
				->findAll();

			$markListData = array();
			foreach ($repositoryData as $item) {
				$markListData[$item->code] = TRUE;
			}
			$markList->createMarkList($markListData);
		};

		return $control;
	}

}
```

Template
````php
	<table n:snippet="table">
		<thead>
		<tr>
			<th>{control markList}</th>
			<th>Foo</th>
		</tr>
		</thead>
		<tbody>
		{foreach repositoryData as $item}
			<tr>
				<td>{control markList, $item->id}</td>
				<td>{$item->foo}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
```

##Events
- onMarkAll (call when mark all items)
- onUnMarkAll (call when unmark all items)
- onMarkItem (call when mark one item)
- onUnMarkItem (call when unmark one item)
- onProcessItem (call when onMarkItem or onUnMarkItem)
- onProcessAll (call when onMarkAll or onUnMarkAll)


###Set templateFile for MarkList
For set templateFile use setTemplateFile()
```php
	/**
	 * CONTROL - MarkList
	 * @return \NasExt\Controls\MarkList
	 */
	protected function createComponentMarkList()
	{
		$control = $this->markListFactory->create();
		$control->setTemplateFile('myTemplate.latte')
		return $control;
	}
```


###Custom options
```php
	/**
	 * CONTROL - MarkList
	 * @return \NasExt\Controls\MarkList
	 */
	protected function createComponentMarkList()
	{
		$control = $this->markListFactory->create();

		$control->rememberMarkList = FALSE;
		$control->setAjaxRequest(FALSE);

		return $control;
	}
```


-----

Repository [http://github.com/nasext/marklist](http://github.com/nasext/marklist).
