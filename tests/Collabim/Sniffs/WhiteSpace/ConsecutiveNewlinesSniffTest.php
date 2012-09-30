<?php

/**
 * Forbides consecutive newlines.
 */
class Collabim_Sniffs_WhiteSpace_ConsecutiveNewlinesSniffTest extends Collabim_TestCase {

	public function testConsecutiveNewLinesUsed() {
		$result = $this->checkFile(__DIR__ . '/data/consecutive-new-lines.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Two or more consecutive newlines are forbidden.',
			$result['errors'][5][1][0]['message']
		);
	}

}
