<?php
/**
 * checks function calls style
 */
class Collabim_Sniffs_Functions_FunctionCallSignatureSniff implements PHP_CodeSniffer_Sniff {

	private $type;


	/**
	 * Returns an array of tokens this test wants to listen for.
	 *
	 * @return array
	 */
	public function register() {
		return array(T_STRING);

	}

	/**
	 * Processes this test, when one of its tokens is encountered.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
	 * @param int                  $stackPtr  The position of the current token
	 *                                        in the stack passed in $tokens.
	 *
	 * @return void
	 */
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$this->type = 'function';
		$tokens = $phpcsFile->getTokens();

		// Find the next non-empty token.
		$openBracket = $phpcsFile->findNext(PHP_CodeSniffer_Tokens::$emptyTokens, ($stackPtr + 1), null, true);

		if ($tokens[$openBracket]['code'] !== T_OPEN_PARENTHESIS) {
			// Not a function call.
			return;
		}

		if (isset($tokens[$openBracket]['parenthesis_closer']) === false) {
			// Not a function call.
			return;
		}

		// Find the previous non-empty token.
		$search = PHP_CodeSniffer_Tokens::$emptyTokens;
		$search[] = T_BITWISE_AND;
		$previous = $phpcsFile->findPrevious($search, ($stackPtr - 1), null, true);
		if ($tokens[$previous]['code'] === T_FUNCTION) {
			// It's a function definition, not a function call.
			//return;
		}

		if ($tokens[$previous]['code'] === T_NEW) {
			// We are creating an object, not calling a function.
			$this->type = 'constructor';
		}

		if ($tokens[$previous]['code'] === T_OBJECT_OPERATOR) {
			// method call on an object, not function call
			$this->type = 'method';
		}

		$closeBracket = $tokens[$openBracket]['parenthesis_closer'];

		if (($stackPtr + 1) !== $openBracket) {
			// Checking this: $value = my_function[*](...).
			$error = sprintf('Space before opening parenthesis of %s call prohibited', $this->type);
			$phpcsFile->addError($error, $stackPtr);
		}

		$next = $phpcsFile->findNext(T_WHITESPACE, ($closeBracket + 1), null, true);
		if ($tokens[$next]['code'] === T_SEMICOLON) {
			if (in_array($tokens[($closeBracket + 1)]['code'], PHP_CodeSniffer_Tokens::$emptyTokens) === true) {
				$error = sprintf('Space after closing parenthesis of %s call prohibited', $this->type);
				$phpcsFile->addError($error, $closeBracket);
			}
		}

		// Check if this is a single line or multi-line function call.
		if ($tokens[$openBracket]['line'] === $tokens[$closeBracket]['line']) {
			$this->processSingleLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
		} else {
			$this->processMultiLineCall($phpcsFile, $stackPtr, $openBracket, $tokens);
		}

	}

	/**
	 * Processes single-line calls.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
	 * @param int                  $stackPtr    The position of the current token
	 *                                          in the stack passed in $tokens.
	 * @param int                  $openBracket The position of the openning bracket
	 *                                          in the stack passed in $tokens.
	 * @param array                $tokens      The stack of tokens that make up
	 *                                          the file.
	 *
	 * @return void
	 */
	public function processSingleLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens) {
		if ($tokens[($openBracket + 1)]['code'] === T_WHITESPACE) {
			// Checking this: $value = my_function([*]...).
			$error = sprintf('Space after opening parenthesis of %s call prohibited', $this->type);
			$phpcsFile->addError($error, $stackPtr);
		}

		$closer = $tokens[$openBracket]['parenthesis_closer'];

		$comma = $phpcsFile->findNext(T_COMMA, $stackPtr, $closer);
		while ($comma !== FALSE) {
			if ($tokens[$comma + 1]['code'] !== T_WHITESPACE) {
				$phpcsFile->addError(sprintf('Space after comma between %s parameters is required', $this->type), $stackPtr);
			}
			if (substr($tokens[$comma + 1]['content'], 0, 2) === '  ') {
				$phpcsFile->addError(
					sprintf('Multiple spaces after comma between %s parameters are prohibited', $this->type), $stackPtr
				);
			}
			if ($tokens[$comma - 1]['code'] === T_WHITESPACE) {
				$phpcsFile->addError(sprintf('Space before comma between %s parameters is prohibited', $this->type), $stackPtr);
			}

			$comma = $phpcsFile->findNext(T_COMMA, $comma + 1, $closer);
		}

		if ($tokens[($closer - 1)]['code'] === T_WHITESPACE) {
			// Checking this: $value = my_function(...[*]).
			$between = $phpcsFile->findNext(T_WHITESPACE, ($openBracket + 1), null, true);

			// Only throw an error if there is some content between the parenthesis.
			// i.e., Checking for this: $value = my_function().
			// If there is no content, then we would have thrown an error in the
			// previous IF statement because it would look like this:
			// $value = my_function( ).
			if ($between !== $closer) {
				$error = sprintf('Space before closing parenthesis of %s call prohibited', $this->type);
				$phpcsFile->addError($error, $closer);
			}
		}

	}

	/**
	 * Processes multi-line calls.
	 *
	 * @param PHP_CodeSniffer_File $phpcsFile   The file being scanned.
	 * @param int                  $stackPtr    The position of the current token
	 *                                          in the stack passed in $tokens.
	 * @param int                  $openBracket The position of the openning bracket
	 *                                          in the stack passed in $tokens.
	 * @param array                $tokens      The stack of tokens that make up
	 *                                          the file.
	 *
	 * @return void
	 */
	public function processMultiLineCall(PHP_CodeSniffer_File $phpcsFile, $stackPtr, $openBracket, $tokens) {
		// We need to work out how far indented the function
		// call itself is, so we can work out how far to
		// indent the arguments.
		$functionIndent = 0;
		for ($i = ($stackPtr - 1); $i >= 0; $i--) {
			if ($tokens[$i]['line'] !== $tokens[$stackPtr]['line']) {
				$i++;
				break;
			}
		}

		if ($tokens[$i]['code'] === T_WHITESPACE) {
			$functionIndent = strlen($tokens[$i]['content']);
		}

		// Each line between the parenthesis should be indented 4 spaces.
		$closeBracket = $tokens[$openBracket]['parenthesis_closer'];
		$lastLine = $tokens[$openBracket]['line'];
		for ($i = ($openBracket + 1); $i < $closeBracket; $i++) {
			// Skip nested function calls.
			if ($tokens[$i]['code'] === T_OPEN_PARENTHESIS) {
				$i = $tokens[$i]['parenthesis_closer'];
				$lastLine = $tokens[$i]['line'];
				continue;
			}

			if ($tokens[$i]['line'] !== $lastLine) {
				$lastLine = $tokens[$i]['line'];

				if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$heredocTokens) === true) {
					// Ignore heredoc indentation.
					continue;
				}

				if (in_array($tokens[$i]['code'], PHP_CodeSniffer_Tokens::$stringTokens) === true) {
					if ($tokens[$i]['code'] === $tokens[($i - 1)]['code']) {
						// Ignore multi-line string indentation.
						continue;
					}
				}

				// We changed lines, so this should be a whitespace indent token, but first make
				// sure it isn't a blank line because we don't need to check indent unless there
				// is actually some code to indent.
				$nextCode = $phpcsFile->findNext(T_WHITESPACE, ($i + 1), ($closeBracket + 1), true);
				if ($tokens[$nextCode]['line'] !== $lastLine) {
					$error = 'Empty lines are not allowed in multi-line function calls';
					$phpcsFile->addError($error, $i);
					continue;
				}

				if ($tokens[$i]['line'] === $tokens[$closeBracket]['line']) {
					// Closing brace needs to be indented to the same level
					// as the function call.
					$expectedIndent = $functionIndent;
				} else {
					$expectedIndent = ($functionIndent + 1);
				}

				if ($tokens[$i]['code'] !== T_WHITESPACE) {
					$foundIndent = 0;
				} else {
					$foundIndent = strlen($tokens[$i]['content']);
				}

				if ($expectedIndent !== $foundIndent) {
					$error = sprintf(
						"Multi-line %s call not indented correctly; expected $expectedIndent tabs but found $foundIndent",
						$this->type
					);
					$phpcsFile->addError($error, $i);
				}
			}
		}

	}

}
