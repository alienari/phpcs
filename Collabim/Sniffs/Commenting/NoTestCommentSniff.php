<?php

class Collabim_Sniffs_Commenting_NoTestCommentSniff implements PHP_CodeSniffer_Sniff {

	private $config;

	private $reasonChecked = false;

	private $testClassExists;

	public function __construct() {
		require_once __DIR__ . '/NoTestCommentSniff/NoTestCommentSniffConfig.php';

		$this->config = new NoTestCommentSniffConfig();
	}

    public function register() {
		return array(
			T_CLASS,
			T_DOC_COMMENT,
		);
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$testClassExists = $this->checkIfTestClassExists($phpcsFile->getFilename());

		if ($testClassExists === true) {
			return;
		}

		$tokens = $phpcsFile->getTokens();

		$currentToken = $tokens[$stackPtr];

		if ($currentToken['type'] === 'T_CLASS' && !$this->reasonChecked) {
			$phpcsFile->addError('Neither test class nor @noTest class annotation exist', $stackPtr);
		}
		else if ($currentToken['type'] === 'T_DOC_COMMENT') {
			$this->checkTestCommentWithReason($currentToken, $phpcsFile, $stackPtr);
		}
	}

	private function checkIfTestClassExists($filePath) {
		if ($this->testClassExists === null) {
			$this->testClassExists = $this->testClassExists($filePath);
		}

		return $this->testClassExists;
	}

	private function testClassExists($filePath) {
		$filePath = str_replace('\\', '/', $filePath);

		$className = pathinfo($filePath, PATHINFO_FILENAME);

		foreach ($this->config->getIncludePaths() as $supportedDir) {
			$namespace = $this->getNamespace($filePath);

			if ($namespace) {
				$testPath = $supportedDir . '/' . $namespace . '/' . $className . 'Test.php';
			}
			else {
				$testPath = $supportedDir . '/' . $className . 'Test.php';
			}

			if (file_exists($testPath)) {
				return true;
			}
		}

		return false;
	}

	private function getNamespace($filePath) {
		// TODO: udělat přes tokeny
		$data = file_get_contents($filePath);

		if (preg_match('~namespace ([^;]+)~', $data, $matches)) {
			return $matches[1];
		}
		else {
			return null;
		}
	}

	private function checkTestCommentWithReason($currentToken, PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		if (strpos($currentToken['content'], '@noTest') === false) {
			return;
		}

		$reasonStartPosition = mb_strpos($currentToken['content'], '@noTest');

		$reason = trim(mb_substr($currentToken['content'], $reasonStartPosition + 8));

		$this->reasonChecked = true;

		if (!$reason) {
			$phpcsFile->addError('Reason does not exist for @noTest class annotation', $stackPtr);

			return;
		}
	}

}//end class

