<?php

/**
 * @property string configFilePath Path to sniff configuration object
 */
class Collabim_Sniffs_Commenting_NoTestCommentSniff implements PHP_CodeSniffer_Sniff {

	/**
	 * @var NoTestCommentSniffConfig
	 */
	private $config;

    public function register() {
		require_once __DIR__ . '/NoTestCommentSniff/NoTestCommentSniffConfig.php';

		$configValues = require $this->configFilePath;

		$this->config = new NoTestCommentSniffConfig(
			$configValues['includePaths'],
			$configValues['diContainerDirectoryPath']
		);

		return array(
			T_CLASS
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

		$classCommentEndStackPtr = $phpcsFile->findPrevious(T_DOC_COMMENT, $stackPtr);

		if ($classCommentEndStackPtr) {
			$this->checkClassComments($phpcsFile, $classCommentEndStackPtr, $stackPtr);
		}
		else {
			$this->noAnnotationExists($phpcsFile, $stackPtr);
		}
	}

	private function checkClassComments(PHP_CodeSniffer_File $phpcsFile, $classCommentPartStackPtr, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		do {
			$classCommentPartStackPtr = $phpcsFile->findPrevious(T_DOC_COMMENT, $classCommentPartStackPtr - 1);

			if ($classCommentPartStackPtr === false) {
				$this->noAnnotationExists($phpcsFile, $stackPtr);

				break;
			}

			$classCommentPartContent = $tokens[$classCommentPartStackPtr]['content'];

			$noTestPosition = mb_strpos($classCommentPartContent, '@noTest');

			if ($noTestPosition !== false) {
				$reason = trim(mb_substr($classCommentPartContent, $noTestPosition + 8));

				if (!$reason) {
					$phpcsFile->addError('Reason does not exist for @noTest class annotation', $classCommentPartStackPtr);

					return;
				}

				break;
			}
		}
		while (true);
	}

	private function noAnnotationExists(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$phpcsFile->addError('Neither test class nor @noTest class annotation exist', $stackPtr);
	}

	private function checkIfIsService($classNameWithNamespace) {
		$containerAsString = file_get_contents($this->getContainerPath());

		$stringToSearchFor = '@return ' . $classNameWithNamespace;

		return (mb_strpos($containerAsString, $stringToSearchFor) !== false);
	}

	private function getContainerPath() {
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
		// TODO: udělat přes tokeny
		$data = file_get_contents($filePath);

		if (preg_match('~namespace ([^;]+)~', $data, $matches)) {
			return $matches[1];
		}
		else {
			return null;
		}
	}

}//end class

