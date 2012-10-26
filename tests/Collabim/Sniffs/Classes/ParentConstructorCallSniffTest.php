<?php


class Collabim_Sniffs_Classes_ParentConstructorCallSniffTest extends Collabim_TestCase
{

	public function testParentConstructorCall_withParrentConstructorCall() {
		$result = $this->checkFile(__DIR__ . '/ParentConstructorCallSniffTest/with-parent-constructor-call.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testParentConstructorCall_withSuppress() {
		$result = $this->checkFile(__DIR__ . '/ParentConstructorCallSniffTest/with-suppress.php');

		$this->assertEquals(0, $result['numErrors']);
	}

	public function testParentConstructorCall_withoutParrentConstructorCall() {
		$result = $this->checkFile(__DIR__ . '/ParentConstructorCallSniffTest/without-parent-constructor-call.php');

		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			$this->getMessage(),
			$result['errors'][6][9][0]['message']
		);
	}

	public function testParentConstructorCall_withParentMethodCall() {
		$result = $this->checkFile(__DIR__ . '/ParentConstructorCallSniffTest/with-parent-method-call.php');

		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			$this->getMessage(),
			$result['errors'][6][9][0]['message']
		);
	}

	public function testParentConstructorCall_withInvalidParentConstructorCall() {
		$result = $this->checkFile(__DIR__ . '/ParentConstructorCallSniffTest/with-invalid-parent-constructor-call.php');

		$this->assertEquals(1, $result['numErrors']);

		$this->assertEquals(
			$this->getMessage(),
			$result['errors'][6][9][0]['message']
		);
	}

	private function getMessage() {
		return 'Missing parent constructor call. If you don\'t call the parent constructor on purpose, suppress this warning by using @SuppressWarnings("CS.ParentConstructorCall") annotation.';
	}

}
