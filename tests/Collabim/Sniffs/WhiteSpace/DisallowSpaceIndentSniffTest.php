<?php
/**
 * Throws errors if spaces are used for indentation.
 *
 */
class Collabim_Sniffs_WhiteSpace_DisallowSpaceIndentSniffTest extends Collabim_TestCase {

	public function testSpacesIndentationUsed() {
		$result = $this->checkFile(__DIR__ . '/data/space-indentation-used.php');
		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			'Tabs must be used to indent lines; spaces are not allowed',
			$result['errors'][3][1][0]['message']
		);
	}

}
