<?php

/**
 * Forcing usage of === and !== in comparisons, unless // intentionally comment is somewhere near
 * @author ondrej
 */
class Collabim_Sniffs_ControlStructures_StrictComparisonOperatorsSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_IS_EQUAL,
			T_IS_NOT_EQUAL,
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
		$name = $tokens[$stackPtr]['content'];

		// find comment on the current line
		$intentionally = $this->scanLineForIntentionallyComment($tokens, $stackPtr + 1);

		// find comment on the previous line
		if (!$intentionally) {
			$prevLineStart = $this->findPreviousLineStart($tokens, $stackPtr);
			if ($prevLineStart) {
				$intentionally = $this->scanLineForIntentionallyComment($tokens, $prevLineStart);
			}
		}

		if (!$intentionally) {
			$phpcsFile->addError(
				sprintf('Non-strict comparison operator %s used without any "intentionally" comment on the same or previous line.', $name),
				$stackPtr
			);
		}

	}//end process()

	private function isIntentionallyComment($token)
	{
		return $token['type'] == 'T_COMMENT' && stripos($token['content'], 'intentional') !== FALSE;
	}

	private function scanLineForIntentionallyComment($tokens, $ptr)
	{
		$curLine = $tokens[$ptr]['line'];
		while (TRUE) {
			if (!isset($tokens[$ptr]) || $tokens[$ptr]['line'] > $curLine) {
				return FALSE;
			}

			if ($this->isIntentionallyComment($tokens[$ptr])) {
				return TRUE;
			}
			$ptr++;
		}
	}

	private function findPreviousLineStart($tokens, $ptr)
	{
		$curLine = $tokens[$ptr]['line'];
		while (TRUE) {
			if (!isset($tokens[$ptr])) {
				return FALSE;
			}
			if ($curLine - 2 == $tokens[$ptr]['line']) {
				return $ptr + 1;
			}
			$ptr--;
		}
	}


}//end class

