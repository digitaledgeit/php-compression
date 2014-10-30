<?php

namespace deit\compression;

/**
 * Archive iterator test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ZipArchiveIteratorTest extends \PHPUnit_Framework_TestCase {

	public function test_count() {

		$zip = new \ZipArchive();
		$zip->open(FIXTURES_DIR.'/test.zip');

		$it = new ZipArchiveIterator($zip);

		$this->assertEquals(6, $it->count()); //4 files and 2 folders

	}

	public function test_iterate() {

		$zip = new \ZipArchive();
		$zip->open(FIXTURES_DIR.'/test.zip');

		$it = new ZipArchiveIterator($zip);

		$this->assertEquals(true, $it->valid());
		$this->assertEquals(0, $it->key());
		$this->assertEquals('README.md', $it->current()->getName());
		$it->next();

		$this->assertEquals(true, $it->valid());
		$this->assertEquals(1, $it->key());
		$this->assertEquals('index.html', $it->current()->getName());
		$it->next();

		$this->assertEquals(true, $it->valid());
		$this->assertEquals(2, $it->key());
		$this->assertEquals('scripts/', $it->current()->getName());
		$it->next();

		$this->assertEquals(true, $it->valid());
		$this->assertEquals(3, $it->key());
		$this->assertEquals('scripts/script.js', $it->current()->getName());
		$it->next();

		$this->assertEquals(true, $it->valid());
		$this->assertEquals(4, $it->key());
		$this->assertEquals('styles/', $it->current()->getName());
		$it->next();

		$this->assertEquals(true, $it->valid());
		$this->assertEquals(5, $it->key());
		$this->assertEquals('styles/style.css', $it->current()->getName());
		$it->next();

		$this->assertEquals(false, $it->valid());
		$it->rewind();
		$this->assertEquals(true, $it->valid());

	}

}
