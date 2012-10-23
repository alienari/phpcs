<?php

class Collabim_Sniffs_Classes_MultipleConstantsOrMembersDeclarationSeparatedByCommasSniffTest
	extends \Collabim_TestCase
{

	public function testConstants()
	{
		$result = $this->checkFile(__DIR__ . '/data/MultipleConstants.php');
		$this->assertEquals(2, $result['numErrors']);

		$this->assertEquals(
			'Multiple constants definition separated by commas is prohibited.',
			$result['errors'][6][25][0]['message']
		);

		$this->assertEquals(
			'Multiple constants definition separated by commas is prohibited.',
			$result['errors'][12][14][0]['message']
		);
	}

	public function testMembers()
	{
		$result = $this->checkFile(__DIR__ . '/data/MultipleMembers.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Multiple members definition separated by commas is prohibited.',
			$result['errors'][6][26][0]['message']
		);
	}

}
