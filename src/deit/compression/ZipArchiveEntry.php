<?php

namespace deit\compression;

/**
 * ZIP entry
 * 
 * @author 	James Newell <james@digitaledgeit.com.au>
 */
class ZipArchiveEntry {

	/**
	 * The name the entry
	 * @var     string
	 */
	private $name;

	/**
	 * The archive containing the entry
	 * @var     \ZipArchive
	 */
	private $archive;

	/**
	 * Constructs the ZIP entry
	 * @param   string      $name       The name of the entry
	 * @param   \ZipArchive $archive    The archive of the entry
	 */
	public function __construct($name, \ZipArchive $archive) {
		$this->name         = $name;
		$this->archive      = $archive;
	}

	/**
	 * Gets the name of the entry
	 * @return  string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the archive containing the entry
	 * @return  ZipArchive
	 */
	public function getArchive() {
		return $this->archive;
	}

	/**
	 * Removes the entry from the archive
	 * @return  $this
	 */
	public function remove() {
	
	}
	
	public function rename($to) {
		
	}

	/**
	 * Extracts the entry to a file on disk
	 * @param   string $path
	 * @return  $this
	 */
	public function extractTo($path) {
		file_put_contents($path, $this->getArchive()->getFromName($this->getName()));
		return $this;
	}

	
}