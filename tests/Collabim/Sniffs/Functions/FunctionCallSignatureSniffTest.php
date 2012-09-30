<?php

class Collabim_Sniffs_Functions_FunctionCallSignatureSniffTest extends Collabim_TestCase {

	public function testRule() {
		$result = $this->checkFile(__DIR__ . '/data/FunctionCallSignatureSniff.php');

		$this->assertEquals(
			'Space after opening parenthesis of function call prohibited',
			$result['errors'][4][1][0]['message']
		);

		$this->assertEquals(
			'Space before opening parenthesis of function call prohibited',
			$result['errors'][5][1][0]['message']
		);

		$this->assertEquals(
			'Space after closing parenthesis of function call prohibited',
			$result['errors'][6][6][0]['message']
		);

		$this->assertEquals(
			'Space before closing parenthesis of function call prohibited',
			$result['errors'][7][17][0]['message']
		);

		$this->assertEquals(
			'Space after opening parenthesis of function call prohibited',
			$result['errors'][8][1][0]['message']
		);

		$this->assertEquals(
			'Space after comma between function parameters is required',
			$result['errors'][9][1][0]['message']
		);

		$this->assertEquals(
			'Multiple spaces after comma between function parameters are prohibited',
			$result['errors'][10][1][0]['message']
		);

		$this->assertEquals(
			'Space before comma between function parameters is prohibited',
			$result['errors'][11][1][0]['message']
		);

		$this->assertEquals(
			'Space after opening parenthesis of method call prohibited',
			$result['errors'][14][7][0]['message']
		);

		$this->assertEquals(
			'Space before opening parenthesis of method call prohibited',
			$result['errors'][15][7][0]['message']
		);

		$this->assertEquals(
			'Space after closing parenthesis of method call prohibited',
			$result['errors'][16][12][0]['message']
		);

		$this->assertEquals(
			'Space before closing parenthesis of method call prohibited',
			$result['errors'][17][23][0]['message']
		);

		$this->assertEquals(
			'Space after opening parenthesis of method call prohibited',
			$result['errors'][18][7][0]['message']
		);

		$this->assertEquals(
			'Space after comma between method parameters is required',
			$result['errors'][19][7][0]['message']
		);

		$this->assertEquals(
			'Multiple spaces after comma between method parameters are prohibited',
			$result['errors'][20][7][0]['message']
		);

		$this->assertEquals(
			'Space before comma between method parameters is prohibited',
			$result['errors'][21][7][0]['message']
		);

		$this->assertEquals(
			'Empty lines are not allowed in multi-line function calls',
			$result['errors'][27][1][0]['message']
		);

		$this->assertEquals(
			'Multi-line method call not indented correctly; expected 1 tabs but found 2',
			$result['errors'][32][1][0]['message']
		);

		$this->assertEquals(
			'Space after opening parenthesis of constructor call prohibited',
			$result['errors'][37][5][0]['message']
		);

		$this->assertEquals(
			'Space before opening parenthesis of constructor call prohibited',
			$result['errors'][38][5][0]['message']
		);

		$this->assertEquals(
			'Space after closing parenthesis of constructor call prohibited',
			$result['errors'][39][10][0]['message']
		);

		$this->assertEquals(
			'Space before closing parenthesis of constructor call prohibited',
			$result['errors'][40][21][0]['message']
		);

		$this->assertEquals(
			'Space after opening parenthesis of constructor call prohibited',
			$result['errors'][41][5][0]['message']
		);

		$this->assertEquals(
			'Space after comma between constructor parameters is required',
			$result['errors'][42][5][0]['message']
		);

		$this->assertEquals(
			'Multiple spaces after comma between constructor parameters are prohibited',
			$result['errors'][43][5][0]['message']
		);

		$this->assertEquals(
			'Space before comma between constructor parameters is prohibited',
			$result['errors'][44][5][0]['message']
		);
	}

}