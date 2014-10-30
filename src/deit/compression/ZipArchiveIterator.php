<?php

namespace deit\compression;

/**
 * ZIP archive iterator
 *
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ZipArchiveIterator implements \Iterator, \Countable {

	/**
	 * The archive index
	 * @var
	 */
	private $index;

	/**
	 * The archive
	 * @var     \ZipArchive
	 */
	private $archive;

	/**
	 * Construct the iterator
	 * @param   \ZipArchive $archive
	 */
	public function __construct(\ZipArchive $archive) {
		$this->index    = 0;
		$this->archive  = $archive;
	}

	/**
	 * Get the number of entries in the archive
	 * @return  int
	 */
	public function count() {
		return $this->archive->numFiles;
	}

	/**
	 * Rewind the iterator
	 */
	public function rewind() {
		$this->index = 0;
	}

	/**
	 * Check whether the iterator has more items
	 */
	public function valid() {
		return $this->index < $this->count();
	}

	/**
	 * Get the current entry
	 * @return  ZipArchiveEntry
	 */
	public function current() {
		return ZipArchiveEntry::fromIndex($this->index, $this->archive);
	}

	/**
	 * Move to the next entry
	 */
	public function next() {
		++$this->index;
	}

	/**
	 * Get the entry key
	 */
	public function key() {
		return $this->index;
	}

}