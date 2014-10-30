<?php

namespace deit\compression;

/**
 * Archive entry test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ZipArchiveEntryTest extends \PHPUnit_Framework_TestCase {

	public function test_fromIndex() {

		$zip = new \ZipArchive();
		$zip->open(FIXTURES_DIR.'/test.zip');

		$entry = ZipArchiveEntry::fromIndex(1, $zip);
		$this->assertEquals(1, $entry->getIndex());
		$this->assertEquals('index.html', $entry->getName());

	}

	public function test_fromName() {

		$zip = new \ZipArchive();
		$zip->open(FIXTURES_DIR.'/test.zip');

		$entry = ZipArchiveEntry::fromName('README.md', $zip);
		$this->assertEquals(0, $entry->getIndex());
		$this->assertEquals('README.md', $entry->getName());

	}

}
