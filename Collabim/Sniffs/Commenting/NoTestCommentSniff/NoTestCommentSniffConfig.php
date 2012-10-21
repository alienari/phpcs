<?php

class NoTestCommentSniffConfig {

	public function getIncludePaths() {
		$basePath = 'D:/Weby/collabim-app/src/tests/phpunit/src';

		return array(
			$basePath . '/library',
			$basePath . '/application/components',
			$basePath . '/application/controllers',
			'D:/Weby/phpcs/tests/Collabim/Sniffs/Commenting/NoTestCommentSniffTest',
		);
	}

}