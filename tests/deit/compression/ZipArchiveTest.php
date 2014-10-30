<?php

namespace deit\compression;

/**
 * Archive test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ZipArchiveTest extends \PHPUnit_Framework_TestCase {

	public function test_createFromFiles() {

		$path   = tempnam(sys_get_temp_dir(), 'zip');
		$zip    = new ZipArchive($path);
		$zip->addFile(__DIR__.'/../../../composer.json');
		$zip->addFile(__DIR__.'/../../../composer.lock');
		$zip->close();

		$this->assertGreaterThan(0, filesize($path));

		unlink($path);
	}

	public function test_createFromFolders() {

		$path   = tempnam(sys_get_temp_dir(), 'zip');
		$zip    = new ZipArchive($path);
		$zip->addFolder(__DIR__.'/../../../vendor');
		$zip->close();

		$this->assertGreaterThan(0, filesize($path));

		unlink($path);
	}

	public function test_iterate() {

		$zip = new ZipArchive(FIXTURES_DIR.'/test.zip');

		$i = 0;
		foreach ($zip as $entry) {
			$this->assertEquals($i++, $entry->getIndex());
		}
		$this->assertEquals(6, $i);

		$zip->close();

	}

}
