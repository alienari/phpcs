<?php

class Collabim_Sniffs_ControlStructures_DisallowedExceptionsInCatchSniffTest extends \Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/DisallowedExceptionsInCatchSniff.php');

		$this->assertEquals(5, $result['numErrors']);

		$this->assertEquals(
			'Catching "\Exception" is prohibited. You can suppress this with @SuppressWarnings(CS.DisallowedExceptionsInCatch) annotation above function or class.',
			$result['errors'][11][3][0]['message']
		);

		$this->assertEquals(
			'Catching "\InvalidArgumentException" is prohibited. You can suppress this with @SuppressWarnings(CS.DisallowedExceptionsInCatch) annotation above function or class.',
			$result['errors'][17][3][0]['message']
		);

		$this->assertEquals(
			'Catching "\Collabim\Exception\InvalidStateException" is prohibited. You can suppress this with @SuppressWarnings(CS.DisallowedExceptionsInCatch) annotation above function or class.',
			$result['errors'][23][3][0]['message']
		);

		$this->assertEquals(
			'Catching "\Collabim\Exception\InvalidStateException" is prohibited. You can suppress this with @SuppressWarnings(CS.DisallowedExceptionsInCatch) annotation above function or class.',
			$result['errors'][34][5][0]['message']
		);

		$this->assertEquals(
			'"throw $e" while catching "\Collabim\Exception\InvalidStateException" is allowed only if it\'s the first interruption statement in catch block. You can suppress this with @SuppressWarnings(CS.DisallowedExceptionsInCatch) annotation above function or class.',
			$result['errors'][58][3][0]['message']
		);
	}

}
