<?php

class Collabim_Sniffs_Files_SameFilenameAsClassNameSniffTest extends Collabim_TestCase {

	public function testRule() {
		$result = $this->checkFile(__DIR__ . '/data/WrongFilename.php');

		$this->assertEquals(
			"Class name 'FooBar' and file name 'WrongFilename.php' do not match.",
			$result['errors'][3][1][0]['message']
		);
	}

}