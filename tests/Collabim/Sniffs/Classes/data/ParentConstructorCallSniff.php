<?php

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
