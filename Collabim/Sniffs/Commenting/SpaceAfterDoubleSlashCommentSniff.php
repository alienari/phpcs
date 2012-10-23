<?php

/**
 * One-line comments must be preceded by a space // example
 * @author ondrej
 */
class Collabim_Sniffs_Commenting_SpaceAfterDoubleSlashCommentSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_COMMENT,
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
		$content = $tokens[$stackPtr]['content'];

		if (substr($content, 0, 2) === '//'
			&& substr($content, 0, 3) !== '// '
			&& substr($content, 0, 3) !== '///') { // Vašek 'regions'
			$phpcsFile->addError('There must be a space before one-line comment content.', $stackPtr);
		}
	}


}//end class

