<?php

namespace Nette;

use Nette\Object as NetteObject;
use UnrelatedClass;

class Foo extends \Nette\Object
{

	public function __construct()
	{

	}

}

class FooBarFoo extends Object
{

	public function __construct()
	{

	}

}

class FooBarFooBar extends NetteObject
{

	public function __construct()
	{

	}

}

class FooBar
{

	public function __construct()
	{

	}

}

class BarFoo extends SomethingElse
{

	public function __construct()
	{
		parent::__construct();
	}

}

class BarFooBar extends UnrelatedClass
{

	public function __construct()
	{
		parent::__construct();
	}

}

class BarFooBarFooBarFoo extends SomethingElse
{

	/**
	 * @SuppressWarnings("CS.ParentConstructorCall")
	 */
	public function __construct()
	{

	}

}

// wrong

class Bar extends SomethingElse
{

	public function __construct()
	{

	}

}

class BarFooBarFoo extends SomethingElse
{

	public function __construct()
	{
		parent::somethingElse();
	}

}

class BarFooBarFooBar extends SomethingElse
{

	public function __construct()
	{
		parent::__construct;
	}

}
