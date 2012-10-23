<?php

/**
 * Exceptions must be thrown and catched by their fully qualified names (with opening backslash).
 * @author ondrej
 */
class Collabim_Sniffs_Namespaces_FullyQualifiedExceptionNamesThrowingAndCatchingSniff implements PHP_CodeSniffer_Sniff
{

	/**
	 * @var array
	 */
	private $alreadyReportedPtrs = array();

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_STRING,
			T_THROW,
			T_CATCH,
		);

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();
		$type = $tokens[$stackPtr]['type'];
		$name = $tokens[$stackPtr]['content'];

		if ($type === 'T_THROW') {
			$newPtr = $phpcsFile->findNext(array(T_WHITESPACE), $stackPtr + 1, NULL, TRUE);
			if ($tokens[$newPtr]['code'] === T_NEW) {
				$whitespacePtr = $phpcsFile->findNext(array(T_WHITESPACE), $newPtr);
				$nsSeparator = $tokens[$whitespacePtr + 1];
				if ($nsSeparator['type'] !== 'T_NS_SEPARATOR' && !in_array($whitespacePtr + 1, $this->alreadyReportedPtrs)) {
					$phpcsFile->addError('Throwing an exception via non fully qualified name.', $whitespacePtr + 1);
					$this->alreadyReportedPtrs[] = $whitespacePtr + 1;
				}
			} else {
				// throwing exception via its variable, that's OK
			}
		} else if ($type === 'T_CATCH') { // catch
			$catchPtr = $tokens[$stackPtr]['parenthesis_opener'] + 1;
			$nsSeparator = $tokens[$catchPtr];
			if ($nsSeparator['type'] !== 'T_NS_SEPARATOR' && !in_array($catchPtr, $this->alreadyReportedPtrs)) {
				$phpcsFile->addError('Catching an exception via non fully qualified name.', $catchPtr);
				$this->alreadyReportedPtrs[] = $catchPtr;
			}
		} else {
			if (substr($name, -strlen('Exception')) !== 'Exception'
				&& substr($name, -strlen('PHPUnit_Framework_AssertionFailedError')) !== 'PHPUnit_Framework_AssertionFailedError') {
				return;
			}

			// referencing exception name - report an error unless it's its declaration
			if ($phpcsFile->findFirstOnLine(array(T_CLASS, T_INTERFACE, T_NAMESPACE), $stackPtr) &&
				!$phpcsFile->findFirstOnLine(array(T_EXTENDS), $stackPtr)) {
				return;
				}

			// exception in use clausule
			if ($phpcsFile->findFirstOnLine(array(T_USE), $stackPtr)) {
				$phpcsFile->addError('Exception reference in use clausule points to its non fully qualified name usage in the same file.', $stackPtr);
				return;
			}

			// exception in called method name or accessed instance/static attribute
			if ($tokens[$stackPtr - 1]['type'] === 'T_OBJECT_OPERATOR' || $tokens[$stackPtr - 1]['type'] === 'T_DOUBLE_COLON') {
				return;
			}

			// exception in declared function name
			if ($phpcsFile->findFirstOnLine(array(T_FUNCTION), $stackPtr)) {
				return;
			}

			// other references
			$whitespacePtr = $phpcsFile->findPrevious(array(T_WHITESPACE, T_OPEN_PARENTHESIS), $stackPtr);
			$nsSeparator = $tokens[$whitespacePtr + 1];
			if ($nsSeparator['type'] !== 'T_NS_SEPARATOR' && !in_array($whitespacePtr + 1, $this->alreadyReportedPtrs)) {
				$phpcsFile->addError('Referencing an exception via non fully qualified name.', $whitespacePtr + 1);
				$this->alreadyReportedPtrs[] = $whitespacePtr + 1;
			}
		}

	}//end process()

}//end class

