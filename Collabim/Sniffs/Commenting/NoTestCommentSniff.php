<?php

/**
 * @property string configFilePath Path to sniff configuration object
 */
class Collabim_Sniffs_Commenting_NoTestCommentSniff implements PHP_CodeSniffer_Sniff {

	private $config;
	private $reasonChecked = false;
	private $testClassExists;
	private $classIsService;
	private $classNamespace;
	private $classNamespaceAlreadyDetected = false;
	private $containerPath;

    public function register() {
		require_once __DIR__ . '/NoTestCommentSniff/NoTestCommentSniffConfig.php';

		$configValues = require $this->configFilePath;

		$this->config = new NoTestCommentSniffConfig(
			$configValues['includePaths'],
			$configValues['diContainerDirectoryPath']
		);

		return array(
			T_CLASS,
			T_DOC_COMMENT,
		);
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$namespace = $this->getNamespace($phpcsFile->getFilename());
		$className = pathinfo($phpcsFile->getFilename(), PATHINFO_FILENAME);
		$classNameWithNamespace = $namespace ? ($namespace . '\\' . $className) : $className;

		if ($this->config->checkDiContainer()) {
			$isService = $this->checkIfIsService($classNameWithNamespace);

			if ($isService === false) {
				return;
			}
		}

		$testClassExists = $this->checkIfTestClassExists($className, $namespace);

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

	private function checkIfIsService($classNameWithNamespace) {
		if ($this->classIsService === null) {
			$this->classIsService = $this->serviceFileExists($classNameWithNamespace);
		}

		return $this->classIsService;
	}

	private function serviceFileExists($classNameWithNamespace) {
		$containerAsString = file_get_contents($this->getContainerPath());

		$stringToSearchFor = '@return ' . $classNameWithNamespace;

		return (mb_strpos($containerAsString, $stringToSearchFor) !== false);
	}

	private function getContainerPath() {
		if (!$this->containerPath) {
			$this->containerPath = $this->findContainerPath();
		}

		return $this->containerPath;
	}

	private function findContainerPath() {
		$diContainerDirectory = $this->config->getDiContainerDirectoryPath();
		$iterator = new DirectoryIterator($diContainerDirectory);

		$lastContainerFileTimestamp = null;
		$lastContainerFileName = null;

		foreach ($iterator as $fileinfo) {
			if ($fileinfo->isFile()) {
				if ($fileinfo->getMTime() > $lastContainerFileTimestamp) {
					$lastContainerFileName = $fileinfo->getFilename();
					$lastContainerFileTimestamp = $fileinfo->getMTime();
				}
			}
		}

		return $diContainerDirectory . '/' . $lastContainerFileName;
	}

	private function checkIfTestClassExists($className, $namespace) {
		if ($this->testClassExists === null) {
			$this->testClassExists = $this->testClassExists($className, $namespace);
		}

		return $this->testClassExists;
	}

	private function testClassExists($className, $namespace) {
		foreach ($this->config->getIncludePaths() as $supportedDir) {
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
		if (!$this->classNamespaceAlreadyDetected) {
			$this->classNamespace = $this->extractNamespace($filePath);

			$this->classNamespaceAlreadyDetected = true;
		}

		return $this->classNamespace;
	}

	private function extractNamespace($filePath) {
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

