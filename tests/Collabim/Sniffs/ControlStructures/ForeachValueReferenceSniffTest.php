<?php

class Collabim_Sniffs_ControlStructures_ForeachValueReferenceSniffTest extends \Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/ForeachValueReferenceSniff.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Passing value as reference in foreach scope is prohibited.',
			$result['errors'][7][15][0]['message']
		);
	}

}