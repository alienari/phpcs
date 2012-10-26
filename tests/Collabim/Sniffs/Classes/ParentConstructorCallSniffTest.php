<?php


class Collabim_Sniffs_Classes_ParentConstructorCallSniffTest extends Collabim_TestCase
{

	public function testParentConstructorCall()
	{
		$result = $this->checkFile(__DIR__ . '/data/ParentConstructorCallSniff.php');
		$this->assertEquals(3, $result['numErrors']);

		$message = 'Missing parent constructor call. If you don\'t call the parent constructor on purpose, suppress this warning by using @SuppressWarnings("CS.ParentConstructorCall") annotation.';

		$this->assertEquals(
			$message,
			$result['errors'][21][9][0]['message']
		);

		$this->assertEquals(
			$message,
			$result['errors'][31][9][0]['message']
		);

		$this->assertEquals(
			$message,
			$result['errors'][41][9][0]['message']
		);
	}

}
