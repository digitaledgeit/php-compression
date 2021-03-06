<?php

namespace deit\compression;
use deit\filesystem\Finder;
use deit\filesystem\Filesystem;
use Traversable;

/**
 * ZIP archive
 * 
 * @author 	James Newell <james@digitaledgeit.com.au>
 */
class ZipArchive implements \ArrayAccess, \IteratorAggregate {
	
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
		
		if (is_file($path)) {
			$flags = null;
		} else if (!file_exists($path)) {
			$flags = \ZipArchive::CREATE;
		} else {
			throw new \InvalidArgumentException(sprintf('Archive path "%s" exists but isn\'t a file.'));
		}
		
		//try and open the archive
		if ($this->archive->open($path, $flags) !== true) {
			throw new \InvalidArgumentException(sprintf('Unable to create/open ZIP archive "%s".', $path));
		}
		
	}

	/**
	 * Get the number of entries in the archive
	 * @return  int
	 */
	public function count() {
		return $this->archive->numFiles;
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

		//check the file exists
		if (!is_file($path)) {
			throw new \InvalidArgumentException("File \"$path\" not found.");
		}

		//set the default name
		if (is_null($name)) {
			$name = basename($path);
		}

		//parse the entry name
		// @see http://us.php.net/manual/en/ziparchive.addfile.php#89813
		// @see http://stackoverflow.com/questions/4620205/php-ziparchive-corrupt-in-windows
		$name = str_replace('\\', '/', ltrim($name, '\\/'));

		//TODO: file descriptor limit restriction may be met, close and re-open the archive
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

		//check the folder exists
		if (!is_dir($path)) {
			throw new \InvalidArgumentException("Folder \"$path\" not found.");
		}

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

		//** I had to use \DirectoryIterator instead of \deit\filesystem\Finder because I kept hitting the directory not empty when trying to remove files after this method
		$it = new \FilesystemIterator($path);
		foreach ($it as $p) {

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
			if (is_numeric($offset)) {
				return ZipArchiveEntry::fromIndex($offset, $this->archive);
			} else {
				return ZipArchiveEntry::fromName($offset, $this->archive);
			}
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
	 * Get an iterator
	 * @return  ZipArchiveIterator
	 */
	public function getIterator() {
		return new ZipArchiveIterator($this->archive);
	}

	/**
	 * @inheritdoc
	 */
	public function __destruct() {
		if ($this->archive !== false) {
			$this->close();
		}
	}
}