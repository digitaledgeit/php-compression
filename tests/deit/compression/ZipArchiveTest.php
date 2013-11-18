<?php

namespace deit\compression;

/**
 * Archive test
 * @author James Newell <james@digitaledgeit.com.au>
 */
class ZipArchiveTest extends \PHPUnit_Framework_TestCase {

	public function test_files() {

		$path   = tempnam(sys_get_temp_dir(), 'zip');
		$zip    = new ZipArchive($path);
		$zip->addFile(__DIR__.'/../../../composer.json');
		$zip->addFile(__DIR__.'/../../../composer.lock');
		$zip->close();

		$this->assertGreaterThan(0, filesize($path));
		echo $path;
	}

	public function test_folders() {

		$path   = tempnam(sys_get_temp_dir(), 'zip');
		$zip    = new ZipArchive($path);
		$zip->addFolder(__DIR__.'/../../../vendor');
		$zip->close();

		$this->assertGreaterThan(0, filesize($path));
		echo $path;
	}

}
