<?php

error_reporting(E_ALL | E_STRICT);

function dump()
{
	foreach(func_get_args() as $var) {
		var_dump($var);
	}
}

require_once(__DIR__ . '/TestCase.php');

$_SERVER['argv'] = array('/usr/bin/php');
$_SERVER['argc'] = 1;

require_once(dirname(__DIR__) . '/PHP_CodeSniffer/CodeSniffer.php');
