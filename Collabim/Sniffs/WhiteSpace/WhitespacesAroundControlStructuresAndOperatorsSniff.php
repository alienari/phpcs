<?php

class Collabim_Sniffs_WhiteSpace_WhitespacesAroundControlStructuresAndOperatorsSniff
	implements PHP_CodeSniffer_Sniff
{

	/**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array_merge(
			PHP_CodeSniffer_Tokens::$assignmentTokens,
			PHP_CodeSniffer_Tokens::$comparisonTokens,
			PHP_CodeSniffer_Tokens::$booleanOperators,
			array(
				T_WHILE,
				T_FOR,
				T_FOREACH,
				T_SWITCH,
				T_IF,
				T_ELSE,
				T_ELSEIF,
				T_CATCH,
				T_ARRAY,
			)
		);

    }//end register()

	/**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile All the tokens found in the document.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
		$rightSide = array(
			T_WHILE,
			T_FOR,
			T_FOREACH,
			T_SWITCH,
			T_IF,
			T_BITWISE_AND
		);
		$noRightSide = array(
			T_ARRAY,
		);

		$tokens = $phpcsFile->getTokens();
		$token = $tokens[$stackPtr];

		if (in_array($token['code'], $rightSide)) {
			if ($tokens[$stackPtr + 1]['content'] !== ' ') {
				$phpcsFile->addError(sprintf('There must be one space on the right side of "%s".', $token['content']), $stackPtr);
			}
		} else if (in_array($token['code'], $noRightSide)) {
			if ($tokens[$stackPtr + 1]['code'] === T_WHITESPACE) {
				$phpcsFile->addError(sprintf('There must be no space on the right side of "%s".', $token['content']), $stackPtr);
			}
		} else { // both sides
			if (($tokens[$stackPtr + 1]['content'] !== ' ' && $tokens[$stackPtr + 1]['content'] !== "\n" && $tokens[$stackPtr + 1]['content'] !== "\r\n")
				|| ($tokens[$stackPtr - 1]['content'] !== ' ' && substr($tokens[$stackPtr - 1]['content'], 0, 1) !== "\t")) {
				$phpcsFile->addError(sprintf('There must be one space on both sides of "%s".', $token['content']), $stackPtr);
			}
		}
	}

}