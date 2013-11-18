<?php

namespace deit\compression;
use deit\filesystem\Finder;
use deit\filesystem\Filesystem;

/**
 * ZIP archive
 * 
 * @author 	James Newell <james@digitaledgeit.com.au>
 */
class ZipArchive implements \ArrayAccess{
	
	/**
	 * The ZIP archive
	 * @var     \ZipArchive
	 */
	private $archive;
	
	/**
	 * Constructs the archive opening a zip file on disk
	 * @param 	string $path
	 * @throws
	 */
	public function __construct($path) {
		$this->archive = new \ZipArchive();
		
		if (file_exists($path)) {
			$flags = null;
			$flags = \ZipArchive::OVERWRITE;
		} else {
			$flags = \ZipArchive::CREATE;
		}
		
		//try and open the archive
		if ($this->archive->open($path, $flags) !== true) {
			throw new \InvalidArgumentException(sprintf('Unable to create/open ZIP archive "%s".', $path));
		}
		
	}

	/**
	 * Adds a new entry
	 * @param   string      $path 		The file or folder which will be copied into the archive
	 * @param   string      $name       The entry name
	 * @return  $this
	 * @throws
	 */
	public function add($path, $name) {
		
		if (is_file($path)) {
			return $this->addFile($path, $name);
		} else if (is_dir($path)) {
			return $this->addFolder($path, $name);
		} else {
			throw new \InvalidArgumentException($path);
		}

		return $this;
	}
	
	/**
	 * Adds a file to the archive
	 * @param 	string 		$path 		The file which will be copied into the archive
	 * @param 	string 		$name 		The entry name
	 * @return 	$this
	 * @throws
	 */
	public function addFile($path, $name = null) {

		//set the default name
		if (is_null($name)) {
			$name = basename($path);
		}

		//parse the entry name
		// @see http://us.php.net/manual/en/ziparchive.addfile.php#89813
		// @see http://stackoverflow.com/questions/4620205/php-ziparchive-corrupt-in-windows
		$name = str_replace('\\', '/', ltrim($name, '\\/'));

		//check the file exists
		if (!is_file($path)) {
			throw new \InvalidArgumentException("File \"$path\" not found.");
		}
		
		if ($this->archive->addFile($path, $name) === false) {
			throw new \RuntimeException(sprintf('Unable to add file "%s" to ZIP archive.', $path));
		}

		return $this;
	}
	
	/**
	 * Adds a folder to the archive

	 * @param 	string   	$path 	    The folder which will be copied into the archive
	 * @param 	string 		$name 		The entry name
	 * @return	$this
	 * @throws
	 */
	public function addFolder($path, $name = null) {
		$fs = new Filesystem();

		$path = rtrim($path, '\\//');

		//set the default name
		if (is_null($name)) {
			$name = basename($path);
			if ($name == '.') {
				$name = basename(dirname($path));
			}
		}

		// @see http://us.php.net/manual/en/ziparchive.addfile.php#89813
		// @see http://stackoverflow.com/questions/4620205/php-ziparchive-corrupt-in-windows
		$name = str_replace('\\', '/', ltrim($name, '\\/'));

		if (!empty($name) && $this->archive->statName($name) === false) {
			if ($this->archive->addEmptyDir($name) === false) {
				throw new \RuntimeException("Unable to add folder \"$path\" to ZIP archive as \"$name\".");
			}
		}

		$f = new Finder($path);
		foreach ($f->depth(1) as $p) {

			if (empty($name)) {
				$n = $fs->getRelativePath($p->getPathname(), $path);
			} else {
				$n = $name.'/'.$fs->getRelativePath($p->getPathname(), $path);
			}

			$this->add($p->getPathname(), $n);
				
		}
		
		return $this;
	}

	/**
	 * Extracts the archive to the destination path
	 * @param   string $path
	 * @return  $this
	 * @throws
	 */
	public function extractTo($path) {
		
		if ($this->archive->extractTo($path) === false) {
			throw new \RuntimeException(sprintf('Unable to extract ZIP archive to "%s".', $path));
		}
		
		return $this;
	}
	
	/**
	 * Closes the archive
	 * @return 	$this
	 * @throws
	 */
	public function close() {
		
		if ($this->archive === false) {
			throw new \RuntineException('ZIP archive is already closed.');
		}
		
		if ($this->archive->close() === false) {
			throw new \RuntineException('Unable to close ZIP archive');
		}
		
		$this->archive = false;
		
		return $this;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset) {
		return $this->archive->locateName($offset) !== false;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet($offset) {
		if ($this->archive->locateName($offset) !== false) {
			return new ZipArchiveEntry($offset, $this->archive);
		} else {
			return null;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value) {
		throw new \IllegalArgumentException();
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset) {
		if ($this->archive->locateName($offset) !== false) {
			$this->archive->deleteName($offset);
		} else {
			return null;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function __destruct() {
		if ($this->archive !== false) $this->close();
	}

}