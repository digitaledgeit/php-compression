# php-compression

High level classes for creating compressed archives in PHP.

# Example

Add entries to an archive:

	$jar = new ZipArchive('test.zip');
	$jar
		->addFile($dir.'/package.json', 'package.json')
		->addFolder($dir.'/scripts',    'scripts')
		->close()
	;
	
Iterate entries in an archive:
			
		$zip = new ZipArchive('test.zip');
		foreach ($zip as $entry) {
			echo $entry->getName().PHP_EOL;
		}
		$zip->close();
	
Extract entries from an archive:
			
	$zip = new ZipArchive('test.zip');
    $zip->extractTo('/tmp'); //all entries
    $zip['package.json']->extractTo('/tmp/package.json'); //a single entry
    $zip->close();
