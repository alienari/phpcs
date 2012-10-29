<?php

class Collabim_Sniffs_NamingConventions_DisallowTooGeneralVariableNamesSniffTest extends Collabim_TestCase {

	public function testRule_simpleScript() {
		$result = $this->checkFile(__DIR__ . '/DisallowTooGeneralVariableNamesSniffTest/simple-script.php');

		$this->assertEquals(
			'Variable name $entity is too general. Please use more specific variable name.',
			$result['errors'][3][1][0]['message']
		);

		$this->assertEquals(
			'Variable name $array is too general. Please use more specific variable name.',
			$result['errors'][5][1][0]['message']
		);
	}

	public function testRule_class() {
		$result = $this->checkFile(__DIR__ . '/DisallowTooGeneralVariableNamesSniffTest/FooClass.php');

		$this->assertEquals(
			'Variable name $results is too general. Please use more specific variable name.',
			$result['errors'][5][10][0]['message']
		);

		$this->assertEquals(
			'Variable name $item is too general. Please use more specific variable name.',
			$result['errors'][8][3][0]['message']
		);

		$this->assertEquals(
			'Variable name $item is too general. Please use more specific variable name.',
			$result['errors'][10][10][0]['message']
		);
	}

}