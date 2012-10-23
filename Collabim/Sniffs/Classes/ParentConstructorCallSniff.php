<?php

/**
 * Exception names must end with "Exception"
 * @author ondrej
 */
class Collabim_Sniffs_Classes_ParentConstructorCallSniff
	extends Collabim_Sniffs_Namespaces_AbstractUseSniffHelper
{

	/**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
		return array(
			T_FUNCTION,
			T_CLASS,
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
		$namespacePtr = $phpcsFile->findPrevious(T_NAMESPACE, $stackPtr);
		$namespace = $namespacePtr ? $this->buildClassNameFromUse($phpcsFile, $namespacePtr, TRUE) : NULL;

		$allowedExtends = array(
			'\Collabim\TestClass',
			'\Nette\Object'
		);

		if ($tokens[$stackPtr]['code'] === T_FUNCTION) {
			return $this->checkConstructor($phpcsFile, $stackPtr, $allowedExtends, $namespace);
		} else if ($tokens[$stackPtr]['code'] === T_CLASS) {
			$classPtr = $phpcsFile->findNext(T_STRING, $stackPtr);
			$class = '\\' . $tokens[$classPtr]['content'];
			if ($namespace) {
				$class = '\\' . $namespace . $class;
			}

			if (!in_array($class, $allowedExtends, TRUE)) {
				return;
			}

			$functionPtr = $phpcsFile->findNext(T_FUNCTION, $tokens[$stackPtr]['scope_opener'], $tokens[$stackPtr]['scope_closer']);
			while ($functionPtr !== FALSE) {
				$namePtr = $phpcsFile->findNext(T_STRING, $functionPtr, $tokens[$stackPtr]['scope_closer']);
				if ($namePtr === FALSE) {
					return;
				}
				if ($tokens[$namePtr]['content'] === '__construct') {
					$phpcsFile->addError(sprintf('%s is in ignored classes but contains constructor. Please update this sniff accordingly.', ltrim($class, '\\')), $stackPtr);
					return;
				}

				$functionPtr = $phpcsFile->findNext(T_FUNCTION, $functionPtr + 1, $tokens[$stackPtr]['scope_closer']);
			}
		}
	}

	private function checkConstructor($phpcsFile, $stackPtr, array $allowedExtends, $namespace)
	{
		$tokens = $phpcsFile->getTokens();
		$functionNamePtr = $phpcsFile->findNext(T_STRING, $stackPtr);
		if ($tokens[$functionNamePtr]['content'] !== '__construct') {
			return;
		}

		$classes = $this->buildClasses($phpcsFile, 0);
		$allClasses = array();
		foreach ($classes as $class) {
			$allClasses[] = $this->getClassOrAs($class);
			if ($this->isAllowedExtends($class, $allowedExtends)) {
				$allowedExtends[] = $this->getClassOrAs($class);
			}
		}

		$classPtr = $phpcsFile->findPrevious(T_CLASS, $stackPtr);
		$extends = $this->findExtendedClassName($phpcsFile, $classPtr);

		if ($extends !== FALSE && substr($extends, 0, 1) !== '\\' && !in_array($extends, $allClasses, TRUE)) {
			if ($namespace) {
				$extends = '\\' . $namespace . '\\' . $extends;
			}
		}

		if ($extends === FALSE || in_array($extends, $allowedExtends, TRUE)) {
			return;
		}

		if ($this->isMethodSuppressed($phpcsFile, $stackPtr)) {
			return;
		}

		$constructor = $tokens[$stackPtr];
		$parentPtr = $phpcsFile->findNext(T_PARENT, $constructor['scope_opener'], $constructor['scope_closer']);

		$message = 'Missing parent constructor call. If you don\'t call the parent constructor on purpose, suppress this warning by using @SuppressWarnings("CS.ParentConstructorCall") annotation. '
			. 'If the parent class does not have a constructor, add this class to the list of ignored classes in this sniff.';
		if ($parentPtr === FALSE) {
			$phpcsFile->addError($message, $stackPtr);
			return;
		}

		$doubleColonPtr = $phpcsFile->findNext(T_WHITESPACE, $parentPtr + 1, NULL, TRUE);
		$constructPtr = $phpcsFile->findNext(T_WHITESPACE, $doubleColonPtr + 1, NULL, TRUE);
		$openParenthesisPtr = $phpcsFile->findNext(T_WHITESPACE, $constructPtr + 1, NULL, TRUE);

		if (($doubleColonPtr === FALSE || $tokens[$doubleColonPtr]['code'] !== T_DOUBLE_COLON)
			|| ($constructPtr === FALSE || $tokens[$constructPtr]['content'] !== '__construct')
			|| $openParenthesisPtr === FALSE || $tokens[$openParenthesisPtr]['code'] !== T_OPEN_PARENTHESIS) {
			$phpcsFile->addError($message, $stackPtr);
		}
	}

	private function isAllowedExtends($class, array $allowedExtends)
	{
		$class = ltrim($class, '\\');
		foreach($allowedExtends as $allowedExtendsClass) {
			$allowedExtendsClass = ltrim($allowedExtendsClass, '\\');
			if (substr($class, 0, strlen($allowedExtendsClass)) === $allowedExtendsClass
				|| substr($class, 1, strlen($allowedExtendsClass)) === $allowedExtendsClass) {
				return TRUE;
			}
		}

		return FALSE;
	}

	private function isMethodSuppressed(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
	{
		$tokens = $phpcsFile->getTokens();

		$ptr = $stackPtr - 1;
		while (TRUE) {
			if (!isset($tokens[$ptr])
				|| ($tokens[$ptr]['type'] !== 'T_PUBLIC'
					&& $tokens[$ptr]['type'] !== 'T_PROTECTED'
					&& $tokens[$ptr]['type'] !== 'T_PRIVATE'
					&& $tokens[$ptr]['type'] !== 'T_WHITESPACE'
					&& $tokens[$ptr]['type'] !== 'T_DOC_COMMENT'
				)) {
				break;
			}
			if ($tokens[$ptr]['type'] === 'T_DOC_COMMENT'
				&& preg_match('/@SuppressWarnings\("?CS(\.ParentConstructorCall)?"?\)/', $tokens[$ptr]['content'])
			) {
				return TRUE;
			}
			$ptr--;
		}
		return FALSE;
	}

	private function getClassOrAs($class, $trim = TRUE)
	{
		$asPos = strpos($class, ' as ');
		if ($asPos === FALSE) {
			if ($trim) {
				$arr = explode('\\', $class);
				return $arr[count($arr)-1];
			} else {
				return $class;
			}
		}

		return substr($class, $asPos + 4);
	}

	/**
	 * Copied from PHP_CodeSniffer_File and fixed for namespaces
     */
    public function findExtendedClassName($phpcsFile, $stackPtr)
    {
		$tokens = $phpcsFile->getTokens();
        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== T_CLASS) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classCloserIndex = $tokens[$stackPtr]['scope_closer'];
        $extendsIndex     = $phpcsFile->findNext(T_EXTENDS, $stackPtr, $classCloserIndex);
        if (false === $extendsIndex) {
            return false;
        }

		$firstNonWhitespacePtr = $phpcsFile->findNext(T_WHITESPACE, $extendsIndex + 1, $classCloserIndex, TRUE);
        $stringPtr = $phpcsFile->findNext(array(T_STRING, T_NS_SEPARATOR, T_WHITESPACE), $firstNonWhitespacePtr, $classCloserIndex);
		$name = '';
		while ($stringPtr !== FALSE && $tokens[$stringPtr]['code'] !== T_WHITESPACE) {
			$name .= $tokens[$stringPtr]['content'];

			$stringPtr = $phpcsFile->findNext(array(T_STRING, T_NS_SEPARATOR, T_WHITESPACE), $stringPtr + 1, $classCloserIndex);
		}

        return $name;

    }//end findExtendedClassName()

}
