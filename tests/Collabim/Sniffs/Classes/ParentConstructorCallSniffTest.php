<?php


class Collabim_Sniffs_Classes_ParentConstructorCallSniffTest extends Collabim_TestCase
{

	public function testParentConstructorCall()
	{
		$result = $this->checkFile(__DIR__ . '/data/ParentConstructorCallSniff.php');
		$this->assertEquals(3, $result['numErrors']);

		$message = 'Missing parent constructor call. If you don\'t call the parent constructor on purpose, suppress this warning by using @SuppressWarnings("CS.ParentConstructorCall") annotation. '
			. 'If the parent class does not have a constructor, add this class to the list of ignored classes in this sniff.';

		$this->assertEquals(
			$message,
			$result['errors'][86][9][0]['message']
		);

		$this->assertEquals(
			$message,
			$result['errors'][96][9][0]['message']
		);

		$this->assertEquals(
			$message,
			$result['errors'][106][9][0]['message']
		);
	}

	public function testCheckIgnoredClass()
	{
		$result = $this->checkFile(__DIR__ . '/data/ignored-class.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Collabim\TestClass is in ignored classes but contains constructor. Please update this sniff accordingly.',
			$result['errors'][24][1][0]['message']
		);
	}

}
