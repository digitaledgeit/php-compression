# php-compression

High level classes for creating compressed archives in PHP.

# Example

Add entries to an archive:

	$jar = new ZipArchive('test.jar');
	$jar
		->addFile($dir.'/package.json', 'package.json')
		->addFolder($dir.'/scripts',    'scripts')
		->close()
	;
	
Extract entries from an archive:
			
	$zip = new ZipArchive('test.jar');
    $zip->extractTo('/tmp'); //all entries
    $zip['package.json']->extractTo('/tmp/package.json'); //a single entry
    $zip->close();
