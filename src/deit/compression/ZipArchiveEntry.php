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
	private $index;

	/**
	 * The archive containing the entry
	 * @var     \ZipArchive
	 */
	private $archive;

	/**
	 * Create a new entry with an index
	 * @param   int             $index
	 * @param   \ZipArchive     $archive
	 * @return  ZipArchiveEntry
	 */
	public static function fromIndex($index, \ZipArchive $archive) {
		return new self($index, $archive);
	}

	/**
	 * Create a new entry with a name
	 * @param   string          $name
	 * @param   \ZipArchive     $archive
	 * @return  ZipArchiveEntry
	 */
	public static function fromName($name, \ZipArchive $archive) {

		$index = $archive->locateName($name);

		if ($index === false) {
			throw new \InvalidArgumentException('Entry does not exist in the archive.');
		}

		return self::fromIndex($index, $archive);
	}

	/**
	 * Constructs the ZIP entry
	 * @param   string      $index      The entry index
	 * @param   \ZipArchive $archive    The entry archive
	 */
	protected function __construct($index, \ZipArchive $archive) {
		$this->index        = $index;
		$this->archive      = $archive;
	}

	/**
	 * Get the entry index
	 * @return  int
	 */
	public function getIndex() {
		return $this->index;
	}

	/**
	 * Get the entry name
	 * @return  string
	 */
	public function getName() {
		return $this->archive->getNameIndex($this->index);
	}

	/**
	 * Remove the entry from the archive
	 * @return  $this
	 */
	public function remove() {
		throw new \InvalidArgumentException;
		return $this;
	}

	/**
	 * Rename the entry in the archive
	 * @return  $this
	 */
	public function rename($to) {
		throw new \InvalidArgumentException;
		return $this;
	}

	/**
	 * Extracts the entry to a file on disk
	 * @param   string $path
	 * @return  $this
	 */
	public function extractTo($path) {
		file_put_contents($path, $this->archive->getFromName($this->getIndex()));
		return $this;
	}

	
}