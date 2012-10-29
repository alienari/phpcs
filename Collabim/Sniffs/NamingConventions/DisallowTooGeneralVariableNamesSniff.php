<?php

class Collabim_Sniffs_NamingConventions_DisallowTooGeneralVariableNamesSniff implements PHP_CodeSniffer_Sniff {

	private $tooGeneralVariableNames = array(
		'result',
		'results',
		'resultList',
		'entity',
		'entities',
		'entityList',
		'object',
		'objects',
		'objectList',
		'array',
		'arrays',
		'list',
		'lists',
		'name',
		'names',
		'nameList',
		'item',
		'items',
		'itemList',
		'field',
		'fields',
		'fieldList',
		'queue',
		'queues',
		'queueList',
		'key',
		'keys',
		'keyList',
		'value',
		'values',
		'valueList',
		'index',
		'indexes',
		'indexList'
	);

    public function register() {
        return array(T_VARIABLE);
    }

	// TODO: implement sniff suppressing
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$tokens = $phpcsFile->getTokens();

		$variableName = $tokens[$stackPtr]['content'];
		$variableNameWithoutDollar = substr($variableName, 1);

		if (in_array($variableNameWithoutDollar, $this->tooGeneralVariableNames)) {
			$phpcsFile->addError(
				'Variable name ' . $variableName . ' is too general. Please use more specific variable name.',
				$stackPtr
			);
		}
    }


}
