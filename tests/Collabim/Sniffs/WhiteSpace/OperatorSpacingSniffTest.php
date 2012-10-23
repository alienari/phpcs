<?php

class Collabim_Sniffs_WhiteSpace_OperatorSpacingSniffTest extends Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/OperatorSpacingSniff.php');

		$this->assertEquals(2, $result['numErrors']);

		$this->assertEquals(
			'Space after minus as a negative value is prohibited',
			$result['errors'][15][16][0]['message']
		);

		$this->assertEquals(
			'Expected 1 space after "-"; 0 found',
			$result['errors'][16][13][0]['message']
		);

	}

}
