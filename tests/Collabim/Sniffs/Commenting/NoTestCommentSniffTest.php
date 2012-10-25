<?php

class Collabim_Sniffs_Commenting_NoTestCommentSniffTest extends Collabim_TestCase {

	public function testRule_noTestAnnotationNotDefined() {
		$result = $this->checkTestFile('/NoTestAnnotationNotDefined/FooClass.php');

		$this->assertEquals(
			'Neither test class nor @noTest class annotation exist',
			$result['errors'][4][1][0]['message']
		);
	}

	public function testRule_noTestAnnotationWithoutReason() {
		$result = $this->checkTestFile('/NoTestAnnotationWithoutReason/FooClass.php');

		$this->assertEquals(
			'Reason does not exist for @noTest class annotation',
			$result['errors'][4][1][0]['message']
		);
	}

	public function testRule_noTestAnnotationWithReasonDefined() {
		$result = $this->checkTestFile('/NoTestAnnotationWithReasonDefined/FooClass.php');

		$this->assertEmpty($result['errors']);
	}

	public function testRule_testExistsAnnotationNotDefined() {
		$result = $this->checkTestFile('/TestExistsAnnotationNotDefined/FooClass.php');

		$this->assertEmpty($result['errors']);
	}

	public function testRule_testExistsAnnotationWithoutReason() {
		$this->markTestIncomplete('Tady by to mělo hlásit, že test existuje a proto není nutná anotace');

		$result = $this->checkTestFile('/TestExistsAnnotationWithoutReason/FooClass.php');

		$this->assertEmpty($result['errors']);
	}

	public function testRule_testExistsAnnotationWithReasonDefined() {
		$this->markTestIncomplete('Tady by to mělo hlásit, že test existuje a proto není nutná anotace');

		$result = $this->checkTestFile('/TestExistsAnnotationWithReasonDefined/FooClass.php');

		$this->assertEmpty($result['errors']);
	}

	private function checkTestFile($path, $checkThisSniffOnly = true) {
		return $this->checkFile(
			__DIR__ . '/NoTestCommentSniffTest' . $path,
			$checkThisSniffOnly,
			__DIR__ . '/NoTestCommentSniffTest/ruleset.xml'
		);
	}

}
