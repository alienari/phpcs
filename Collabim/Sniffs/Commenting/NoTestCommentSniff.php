<?php

/**
 * @property string $testPaths
 * @property string $diContainerDirectoryPath
 */
class Collabim_Sniffs_Commenting_NoTestCommentSniff implements PHP_CodeSniffer_Sniff {

    public function register() {
		return array(
			T_CLASS
		);
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$namespace = $this->getNamespace($phpcsFile->getFilename());
		$className = pathinfo($phpcsFile->getFilename(), PATHINFO_FILENAME);

		if ($this->shouldBeSkiped($className, $namespace)) {
			return;
		}

		$classNameWithNamespace = $namespace ? ($namespace . '\\' . $className) : $className;

		if ($this->diContainerDirectoryPath) {
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

	private function shouldBeSkiped($className, $namespace) {
		// Mappers should have long tests, not src tests
		if (substr($className, -6) === 'Mapper') {
			return true;
		}

		return false;
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

			if (mb_strpos($classCommentPartContent, '@noTest') !== false) {
				if (!$this->reasonDefined($classCommentPartContent)) {
					$phpcsFile->addError('Reason does not exist for @noTest class annotation', $classCommentPartStackPtr);
				}

				return;
			}
		}
		while (true);
	}

	private function noAnnotationExists(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$phpcsFile->addError('Neither test class nor @noTest class annotation exist', $stackPtr);
	}

	private function reasonDefined($classCommentPartContent) {
		return (bool) preg_match('~@noTest[\s]+[^\s]+~i', $classCommentPartContent);
	}

	private function checkIfIsService($classNameWithNamespace) {
		$containerAsString = file_get_contents($this->getContainerPath());

		$stringToSearchFor = '@return ' . $classNameWithNamespace;

		return (mb_strpos($containerAsString, $stringToSearchFor) !== false);
	}

	private function getContainerPath() {
		$iterator = new DirectoryIterator($this->diContainerDirectoryPath);

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

		return $this->diContainerDirectoryPath . '/' . $lastContainerFileName;
	}

	private function checkIfTestClassExists($className, $namespace) {
		foreach ($this->getTestPaths() as $supportedDir) {
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

	private function getTestPaths() {
		if (substr($this->testPaths, -1, 1) === ';') {
			return explode(';', substr($this->testPaths, 0, -1));
		}
		else {
			return explode(';', $this->testPaths);
		}
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

