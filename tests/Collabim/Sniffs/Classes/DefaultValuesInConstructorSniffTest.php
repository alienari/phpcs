<?php

class Collabim_Sniffs_Classes_DefaultValuesInConstructorSniffTest extends \Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/DefaultValuesInConstructorSniff.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Default values for members must be set in the constructor.',
			$result['errors'][6][15][0]['message']
		);
	}

}