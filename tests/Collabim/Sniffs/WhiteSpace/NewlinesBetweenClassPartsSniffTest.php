<?php

class Collabim_Sniffs_WhiteSpace_NewlinesBetweenClassPartsSniffTest extends Collabim_TestCase {

	public function testEmptyClass() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/EmptyClass.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testClassWithConstantsOnlyAndWithEmptyLineMissing() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/ClassWithConstantsOnlyAndWithEmptyLineMissing.php');

		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Line after last constant must be empty.',
			$result['errors'][8][2][0]['message']
		);
	}

	/**
	 * This is invalid syntax according to our coding standards, but this sniff should report 0 errors
	 */
	public function testClassWithConstantsOnlySpecialCaseWithMultipleDeclaration() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/ClassWithConstantsOnlySpecialCaseWithMultipleDeclaration.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testConstantsBeforeClassMembers() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/ConstantsBeforeClassMembers.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'All constants must be before class members.',
			$result['errors'][10][2][0]['message']
		);
	}

}
