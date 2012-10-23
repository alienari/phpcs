<?php

/**
 * Disallow instantiating new object without constructor parenthesis: $x = new stdClass;
 * @author ondrej
 */
class Collabim_Sniffs_Classes_ConstructorParenthesisWhileInstantiatingSniff
	implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_NEW,
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
		$token = $tokens[$stackPtr];
		$space = $tokens[$stackPtr + 1];
		if ($space['type'] !== 'T_WHITESPACE' || $space['content'] !== ' ') {
			$phpcsFile->addError('Invalid new clausule.', $stackPtr);
		}
		$ptr = $stackPtr+2;
		while (TRUE) {
			if (!isset($tokens[$ptr])
				|| ($tokens[$ptr]['type'] !== 'T_NS_SEPARATOR'
					&& $tokens[$ptr]['type'] !== 'T_STRING'
					&& $tokens[$ptr]['type'] !== 'T_VARIABLE'
					&& $tokens[$ptr]['type'] !== 'T_STATIC'
					&& $tokens[$ptr]['content'] !== '['
					&& $tokens[$ptr]['type'] !== 'T_LNUMBER'
					&& $tokens[$ptr]['content'] !== ']'
				)) {
					break;
				}
			$ptr++;
		}
		if (!isset($tokens[$ptr]) || $tokens[$ptr]['type'] !== 'T_OPEN_PARENTHESIS') {
			$phpcsFile->addError('Class must have constructor parentheses while it is instantiated.', $ptr);
		}
	}

}//end class

