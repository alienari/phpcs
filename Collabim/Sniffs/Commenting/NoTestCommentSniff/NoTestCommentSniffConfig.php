<?php

class NoTestCommentSniffConfig {

	private $includePaths;
	private $diContainerPath;

	public function __construct($includePaths, $diContainerPath) {
		$this->includePaths = $includePaths;
		$this->diContainerPath = $diContainerPath;
	}

	public function getIncludePaths() {
		return $this->includePaths;
	}

	public function checkDiContainer() {
		return ($this->diContainerPath !== null);
	}

	public function getDiContainerDirectoryPath() {
		return $this->diContainerPath;
	}

}
