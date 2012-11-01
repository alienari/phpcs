<?php

class WhitespacesBetweenMethods {

	public function __construct() {
		// #1 method
	}

	public function somePublicMethod() {
		// #2 method
	}
	public function someDifferentPublicMethod() {
		// #3 method
	}

	private function somePrivateFunction() {
		// #4 method
	}
}
