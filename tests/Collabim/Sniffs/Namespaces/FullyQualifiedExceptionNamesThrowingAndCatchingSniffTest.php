<?php

class Collabim_Sniffs_Namespaces_FullyQualifiedExceptionNamesThrowingAndCatchingSniffTest extends Collabim_TestCase
{

	public function testRule()
	{
		$result = $this->checkFile(__DIR__ . '/data/FullyQualifiedExceptionNamesThrowingAndCatchingSniff.php');
		$this->assertEquals(4, $result['numErrors']);

		$this->assertEquals(
			'Throwing an exception via non fully qualified name.',
			$result['errors'][26][11][0]['message']
		);

		$this->assertEquals(
			'Throwing an exception via non fully qualified name.',
			$result['errors'][27][11][0]['message']
		);

		$this->assertEquals(
			'Catching an exception via non fully qualified name.',
			$result['errors'][31][10][0]['message']
		);

		$this->assertEquals(
			'Catching an exception via non fully qualified name.',
			$result['errors'][37][10][0]['message']
		);
	}

	public function testTextDataMigration()
	{
		$result = $this->checkFile(__DIR__ . '/data/GeneralExceptionCatchingSniff.php');
		$this->assertEquals(0, $result['numErrors']);
	}

}
