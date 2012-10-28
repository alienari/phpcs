<?php

class Collabim_Sniffs_Files_NoNewlineAtEndOfFileSniffTest extends Collabim_TestCase {

	public function testNewline() {
		$result = $this->checkFile(__DIR__ . '/NoNewlineAtEndOfFileSniffTest/newline.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testNewlineScript() {
		$result = $this->checkFile(__DIR__ . '/NoNewlineAtEndOfFileSniffTest/newline-script.php');
		$this->assertEquals(0, $result['numErrors']);
	}

	public function testNoNewline()	{
		$result = $this->checkFile(__DIR__ . '/NoNewlineAtEndOfFileSniffTest/nonewline.php');

		$this->assertEquals(1, $result['numErrors']);
		$this->assertEquals(
			"Missing plain newline at end of file nonewline.php.",
			$result['errors'][6][1][0]['message']
		);
	}

	public function testMultipleNewlines() {
		$result = $this->checkFile(__DIR__ . '/NoNewlineAtEndOfFileSniffTest/multiplenewlines.php');

		$this->assertEquals(1, $result['numErrors']);
		$this->assertEquals(
			"Only simple newline allowed after file closing bracket in multiplenewlines.php.",
			$result['errors'][7][1][0]['message']
		);
	}

}