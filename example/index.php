<?php
# require JacksonJeans/php-file
require_once('src/JacksonJeans/FileInterface.class.php');
require_once('src/JacksonJeans/FileException.class.php');
require_once('src/JacksonJeans/AESCryptoStreamFactory.class.php');
require_once('src/JacksonJeans/File.class.php');
require_once('src/JacksonJeans/FileInfo.class.php');
require_once('src/JacksonJeans/FileList.class.php');

# require JacksonJeans/php-csv
require_once('src/JacksonJeans/CsvFile.class.php');
require_once('src/JacksonJeans/CreateCsvFile.class.php');

header('Content-Type: text/plain');

# example values
$filepath = 'test/addresses.csv';

$csvstr = "Name,Nachname,Anschrift,Stadt,Staat,Postleitzahl\nJohn,Doe,120 jefferson st.,Riverside, NJ, 0807";

$resource = fopen($filepath, 'rw+');

# open csv with filepath
$CsvFile = new JacksonJeans\CsvFile($filepath, ',', '"', "\\");
$CsvFile->setDelimiter(',');
$CsvFile->setEnclosure('');
$CsvFile->setEscape("\\");

if ($CsvFile->isWellFormed()) {
    print_r("Open with string filepath: CSV file is well-formed\n\n");
    foreach ($CsvFile->toArray() as $key => $value) {
        echo var_dump($key, $value) . "\n";
    }
} else {
    print_r("Open with string filepath: CSV file isnt well-formed\n\n");
}

# close file
$CsvFile->close();

# open string as csv
$CsvFile = new JacksonJeans\CsvFile($csvstr, ',', '', "\\");
if ($CsvFile->isWellFormed()) {
    print_r("Open with string: CSV string is well-formed\n\n");
    foreach ($CsvFile->toArray() as $key => $value) {
        echo var_dump($key, $value) . "\n";
    }
} else {
    print_r("Open with string: CSV string isnt well-formed\n\n");
}

# open csv with a resource
$CsvFile = new JacksonJeans\CsvFile($resource, ',', '"', "\\");
$CsvFile->setDelimiter(',');
$CsvFile->setEnclosure('');
$CsvFile->setEscape("\\");
if ($CsvFile->isWellFormed()) {
    print_r("Open with resource: CSV file is well-formed\n\n");
    foreach ($CsvFile->toArray() as $key => $value) {
        echo var_dump($key, $value) . "\n";
    }
} else {
    print_r("Open with resource: CSV file isnt well-formed\n\n");
}
echo "\nread csv as assoc array\n\n";
# read csv as assoc array
foreach ($CsvFile->toArray() as $array) {
    echo "{$array['Name']}\t\t{$array['Nachname']}\t\t\t{$array['Anschrift']}\t\t\t{$array['Stadt']}\n";
}

echo "\nget json \n\n";

# toJSON
$json = $CsvFile->toJson();
echo var_dump($json) . "\n";


echo "\nget objects \n\n";
# toObject
$objects = $CsvFile->toObject();
foreach ($objects as $object) {
    echo "{$object->Name}\t\t{$object->Nachname}\t\t\t{$object->Anschrift}\t\t\t{$object->Stadt}\n";
}

# close csv
$CsvFile->close();

# create csv

$newCSVFile = new JacksonJeans\CreateCsvFile;

$destination = 'test/csv.csv';
$delimiter = ',';
$enclosure = '"';
$escape = "\\";
$encloseAll = true;
$nullToMysqlNull = false;

$newCSVFile->setHeader(array('Name', 'Nachname', 'Anschrift', 'Stadt'));
$newCSVFile->setLine(array('John', 'Doe', '120 jefferson st.', 'Riverside'));

$str = $newCSVFile->get($delimiter, $enclosure, $escape, $encloseAll, $nullToMysqlNull);

echo "\n\nOutput from new Csv File:\n\n".$str."\n\n";

$create = $newCSVFile->create($destination, $delimiter, $enclosure, $escape, $encloseAll, $nullToMysqlNull);

if ($newCSVFile->isWellFormed()) {
    print_r("Open with resource: CSV file is well-formed\n\n");
    foreach ($newCSVFile->toArray() as $key => $value) {
        echo var_dump($key, $value) . "\n";
    }
} else {
    print_r("Open with resource: CSV file isnt well-formed\n\n");
}

echo "\n\nDaten zur Datei: \n\tName:\t\t\t{$newCSVFile->getName()}\n\tDateipfad:\t\t{$newCSVFile->getFilepath()}\n\tBesitzer:\t\t{$newCSVFile->getOwnerId()}\n\tLetzte Aenderung:\t{$newCSVFile->getLastChange('d.m.Y H:i:s')}\n\tGroesse formatiert:\t{$newCSVFile->getSize(true)}\n\tGroesse in Bytes:\t{$newCSVFile->getSize()}\n\tSuffix:\t\t\t{$newCSVFile->getSuffix()}\n\tMIME-ContentType:\t{$newCSVFile->getMimeContentType()}\n";
