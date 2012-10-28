<?php

/**
 * @author ondrej
 */
class Collabim_Sniffs_Files_NoNewlineAtEndOfFileSniff
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
			T_OPEN_TAG,
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
		$filename = pathinfo($phpcsFile->getFilename(), PATHINFO_FILENAME);
		$tokens = $phpcsFile->getTokens();
		$token = end($tokens);
		$tokenPtr = key($tokens);

		if ($token['type'] !== "T_WHITESPACE" || ($token['content'] !== "\n" && $token['content'] !== "\r\n")) {
			$phpcsFile->addError("Missing plain newline at end of file $filename.php.", $tokenPtr);
		} elseif ($tokens[$tokenPtr - 1]['content'] === "\n" || $tokens[$tokenPtr - 1]['content'] === "\r\n") {
			$phpcsFile->addError("Only simple newline allowed after file closing bracket in $filename.php.", $tokenPtr);
		}
	}

}//end class
