<?php

/**
 * Exception names must end with "Exception"
 * @author ondrej
 */
class Collabim_Sniffs_Classes_ParentConstructorCallSniff
	extends Collabim_Sniffs_Namespaces_AbstractUseSniffHelper {

	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(
			T_FUNCTION,
			T_CLASS,
		);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token in the
	 *                                        stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_FUNCTION) {
			$this->checkConstructor($phpcsFile, $stackPtr);
		}
	}

	private function checkConstructor($phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();
		$functionNamePtr = $phpcsFile->findNext(T_STRING, $stackPtr);
		if ($tokens[$functionNamePtr]['content'] !== '__construct') {
			return;
		}

		if ($this->isMethodSuppressed($phpcsFile, $stackPtr)) {
			return;
		}

		$constructor = $tokens[$stackPtr];
		$parentPtr = $phpcsFile->findNext(T_PARENT, $constructor['scope_opener'], $constructor['scope_closer']);

		$message = 'Missing parent constructor call. '
			.'If you don\'t call the parent constructor on purpose, suppress this warning by using @SuppressWarnings("CS.ParentConstructorCall") annotation.';
		if ($parentPtr === false) {
			$phpcsFile->addError($message, $stackPtr);
			return;
		}

		$doubleColonPtr = $phpcsFile->findNext(T_WHITESPACE, $parentPtr + 1, null, true);
		$constructPtr = $phpcsFile->findNext(T_WHITESPACE, $doubleColonPtr + 1, null, true);
		$openParenthesisPtr = $phpcsFile->findNext(T_WHITESPACE, $constructPtr + 1, null, true);

		if (($doubleColonPtr === false || $tokens[$doubleColonPtr]['code'] !== T_DOUBLE_COLON)
			|| ($constructPtr === false || $tokens[$constructPtr]['content'] !== '__construct')
			|| $openParenthesisPtr === false || $tokens[$openParenthesisPtr]['code'] !== T_OPEN_PARENTHESIS
		) {
			$phpcsFile->addError($message, $stackPtr);
		}
	}

	private function isMethodSuppressed(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$ptr = $stackPtr - 1;
		while (true) {
			if (!isset($tokens[$ptr])
				|| ($tokens[$ptr]['type'] !== 'T_PUBLIC'
					&& $tokens[$ptr]['type'] !== 'T_PROTECTED'
					&& $tokens[$ptr]['type'] !== 'T_PRIVATE'
					&& $tokens[$ptr]['type'] !== 'T_WHITESPACE'
					&& $tokens[$ptr]['type'] !== 'T_DOC_COMMENT'
				)
			) {
				break;
			}
			if ($tokens[$ptr]['type'] === 'T_DOC_COMMENT'
				&& preg_match('/@SuppressWarnings\("?CS(\.ParentConstructorCall)?"?\)/', $tokens[$ptr]['content'])
			) {
				return true;
			}
			$ptr--;
		}
		return false;
	}

}
