<?php

class Collabim_Sniffs_WhiteSpace_WhitespacesAroundControlStructuresAndOperatorsSniffTest
	extends \Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/WhitespacesAroundControlStructuresAndOperatorsSniff.php');

		$this->assertEquals(6, $result['numErrors']);

		$this->assertEquals(
			'There must be one space on the right side of "if".',
			$result['errors'][42][1][0]['message']
		);

		$this->assertEquals(
			'There must be one space on both sides of "===".',
			$result['errors'][42][6][0]['message']
		);

		$this->assertEquals(
			'There must be one space on both sides of "else".',
			$result['errors'][44][2][0]['message']
		);

		$this->assertEquals(
			'There must be one space on the right side of "foreach".',
			$result['errors'][48][1][0]['message']
		);

		$this->assertEquals(
			'There must be no space on the right side of "array".',
			$result['errors'][52][1][0]['message']
		);

		$this->assertEquals(
			'There must be one space on the right side of "switch".',
			$result['errors'][56][1][0]['message']
		);
	}

}
