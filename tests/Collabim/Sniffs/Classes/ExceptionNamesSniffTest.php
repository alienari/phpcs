<?php

class Collabim_Sniffs_Classes_ExceptionNamesSniffTest extends \Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/ExceptionNamesSniff.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Exception class names must end with "Exception".',
			$result['errors'][8][1][0]['message']
		);
	}

}