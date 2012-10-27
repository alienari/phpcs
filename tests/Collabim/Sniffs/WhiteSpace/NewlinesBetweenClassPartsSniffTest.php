<?php

class Collabim_Sniffs_WhiteSpace_NewlinesBetweenClassPartsSniffTest extends Collabim_TestCase {

	public function testEmptyClass() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/EmptyClass.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testClassWithContantsAndMembers() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/ClassWithContantsAndMembers.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testClassWithConstantsOnlyAndWithEmptyLineMissing() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/ClassWithConstantsOnlyAndWithEmptyLineMissing.php');

		$this->assertEquals(2, $result['numErrors']);

		$this->assertEquals(
			'There must be NO empty lines between constant declarations.',
			$result['errors'][7][1][0]['message']
		);

		$this->assertEquals(
			'Line after last constant must be empty.',
			$result['errors'][9][2][0]['message']
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
			$result['errors'][7][2][0]['message']
		);
	}

	public function testClassMembers() {
		$result = $this->checkFile(__DIR__ . '/NewlinesBetweenClassPartsSniffTest/ClassMembers.php');
		$this->assertEquals(4, $result['numErrors']);

		$this->assertEquals(
			'There should be no empty lines between member variables.',
			$result['errors'][7][1][0]['message']
		);

		$this->assertEquals(
			'There should be no empty lines between member variables.',
			$result['errors'][9][1][0]['message']
		);

		$this->assertEquals(
			'There should be no empty lines between member variables.',
			$result['errors'][13][1][0]['message']
		);

		$this->assertEquals(
			'Line after function close parenthesis must be empty.',
			$result['errors'][24][2][0]['message']
		);
	}

}
