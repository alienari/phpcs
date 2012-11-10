<?php

require_once(__DIR__ . '/IncorrectSniffTestCaseNameException.php');

class Collabim_TestCase extends PHPUnit_Framework_TestCase {

	protected function checkFile($file, $checkThisSniffOnly = TRUE) {
		$result = $this->processFile($file, $checkThisSniffOnly);
		return $result[$file];
	}

	/**
	 * @param string $file Path to file
	 * @return array Collected violations
	 */
	private function processFile($file, $checkThisSniffOnly = TRUE) {
		$cs = new PHP_CodeSniffer();

		if ($checkThisSniffOnly) {
			$sniffs = array(strtolower(substr(get_class($this), 0, -strlen('Test'))));
		} else {
			$sniffs = array();
		}

		$cs->process($file, '../Collabim/ruleset-testing.xml', $sniffs);

		if ($checkThisSniffOnly) {
			$listenersProperty = new ReflectionProperty('PHP_CodeSniffer', 'listeners');
			$listenersProperty->setAccessible(TRUE);
			$listeners = $listenersProperty->getValue($cs);

			if (count($listeners) !== 1) {
				throw new \Collabim_IncorrectSniffTestCaseNameException('Sniff test case name must match sniff class name');
			}
		}

		return $cs->getFilesErrors();
	}

}
