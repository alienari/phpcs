<?php


class Collabim_Sniffs_WhiteSpace_NewlinesBetweenClassPartsSniffTest extends Collabim_TestCase
{

	public function testHashBang()
	{
		$result = $this->checkFile(__DIR__ . '/data/hashbang.php');
		$this->assertEquals(0, $result['numErrors']);
	}

	public function testClassWithConstantsOnlyAndWithEmptyLineMissing()
	{
		$result = $this->checkFile(__DIR__ . '/data/ClassWithConstantsOnlyAndWithEmptyLineMissing.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Line after last constant must be empty.',
			$result['errors'][8][2][0]['message']
		);
	}

	public function testClassWithConstantsOnlySpecialCaseWithMultipleDeclaration()
	{
		// this is invalid syntax according to our coding standards
		// but this sniff should report 0 errors

		$result = $this->checkFile(__DIR__ . '/data/ClassWithConstantsOnlySpecialCaseWithMultipleDeclaration.php');
		$this->assertEquals(0, $result['numErrors']);
	}

	public function testConstantsBeforeClassMembers()
	{
		$result = $this->checkFile(__DIR__ . '/data/ConstantsBeforeClassMembers.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'All constants must be before class members.',
			$result['errors'][10][2][0]['message']
		);
	}

}
