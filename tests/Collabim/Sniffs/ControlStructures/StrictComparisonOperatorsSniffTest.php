<?php

class Collabim_Sniffs_ControlStructures_StrictComparisonOperatorsSniffTest extends \Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/StrictComparisonOperator.php');
		$this->assertEquals(2, $result['numErrors']);

		$this->assertEquals(
			'Non-strict comparison operator == used without any "intentionally" comment on the same or previous line.',
			$result['errors'][11][3][0]['message']
		);

		$this->assertEquals(
			'Non-strict comparison operator != used without any "intentionally" comment on the same or previous line.',
			$result['errors'][13][3][0]['message']
		);
	}

}
