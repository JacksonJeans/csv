# PHP-CSV Class

PHP-CSV class was developed to simplify the handling of csv files.

With PHP-CSV you can read and create CSV files. You can get the return as associative array, JSON or in objects. Set the header line and parse your CSV to the desired format. 

[TOC]



## Install

The install is simple. 

Requirements is at least PHP 5.3.3, [JacksonJeans/php-file](https://github.com/JacksonJeans/php-file) package, openssl with the sodium package.

### By Hand

Move the files to the appropriate directory

```php
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

# namespace
use JacksonJeans;

# example values
$filepath = 'test/addresses.csv';

# open csv with filepath
$CsvFile = new JacksonJeans\CsvFile($filepath, ',', '"', "\\");
```

### Composer

Or with Composer

```
composer require jacksonjeans/csv
```

## Usage

The use is simple

### Open filepath

```php
# example values
$filepath = 'test/addresses.csv';

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
```

### Open resource

```php
# example values
$resource = fopen($filepath, 'rw+');

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

$CsvFile->close();
```

### Open with CSV string

```php
# example values
$csvstr = "Name,Nachname,Anschrift,Stadt,Staat,Postleitzahl\nJohn,Doe,120 jefferson st.,Riverside, NJ, 0807";

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
```

### toArray

```php
# read csv as assoc array
foreach ($CsvFile->toArray() as $array) {
    echo "{$array['Name']}\t\t{$array['Nachname']}\t\t\t{$array['Anschrift']}\t\t\t{$array['Stadt']}\n";
}
```

### toObject

```php
# toObject
$objects = $CsvFile->toObject();
foreach ($objects as $object) {
    echo "{$object->Name}\t\t{$object->Nachname}\t\t\t{$object->Anschrift}\t\t\t{$object->Stadt}\n";
}
```

### toJSON

```php
# toJSON
$json = $CsvFile->toJson();
echo var_dump($json) . "\n";
```

### Create

```php
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
```

### setHeaderLine

CsvFile::setHeaderLine(int) is a method to set the line of the CSV to be used for the associative indices.

The headerLine is by default 0.

```php
# set header
$CsvFile->setHeaderLine(0);
```

### output

```
Open with string filepath: CSV file is well-formed

int(1)
array(6) {
  ["Name"]=>
  string(4) "John"
  ["Nachname"]=>
  string(3) "Doe"
  ["Anschrift"]=>
  string(17) "120 jefferson st."
  ["Stadt"]=>
  string(9) "Riverside"
  ["Staat"]=>
  string(3) " NJ"
  ["Postleitzahl"]=>
  string(6) " 08075"
}

int(2)
array(6) {
  ["Name"]=>
  string(4) "Jack"
  ["Nachname"]=>
  string(8) "McGinnis"
  ["Anschrift"]=>
  string(12) "220 hobo Av."
  ["Stadt"]=>
  string(5) "Phila"
  ["Staat"]=>
  string(3) " PA"
  ["Postleitzahl"]=>
  string(5) "09119"
}

int(3)
array(6) {
  ["Name"]=>
  string(13) "John "Da Man""
  ["Nachname"]=>
  string(6) "Repici"
  ["Anschrift"]=>
  string(17) "120 Jefferson St."
  ["Stadt"]=>
  string(9) "Riverside"
  ["Staat"]=>
  string(3) " NJ"
  ["Postleitzahl"]=>
  string(5) "08075"
}

int(4)
array(6) {
  ["Name"]=>
  string(7) "Stephen"
  ["Nachname"]=>
  string(5) "Tyler"
  ["Anschrift"]=>
  string(32) "7452 Terrace "At the Plaza" road"
  ["Stadt"]=>
  string(8) "SomeTown"
  ["Staat"]=>
  string(2) "SD"
  ["Postleitzahl"]=>
  string(6) " 91234"
}

int(5)
array(6) {
  ["Name"]=>
  string(0) ""
  ["Nachname"]=>
  string(8) "Blankman"
  ["Anschrift"]=>
  string(0) ""
  ["Stadt"]=>
  string(8) "SomeTown"
  ["Staat"]=>
  string(3) " SD"
  ["Postleitzahl"]=>
  string(6) " 00298"
}

int(6)
array(6) {
  ["Name"]=>
  string(21) "Joan "the bone", Anne"
  ["Nachname"]=>
  string(3) "Jet"
  ["Anschrift"]=>
  string(19) "9th, at Terrace plc"
  ["Stadt"]=>
  string(11) "Desert City"
  ["Staat"]=>
  string(2) "CO"
  ["Postleitzahl"]=>
  string(5) "00123"
}

Open with string: CSV string is well-formed

int(1)
array(6) {
  ["Name"]=>
  string(4) "John"
  ["Nachname"]=>
  string(3) "Doe"
  ["Anschrift"]=>
  string(17) "120 jefferson st."
  ["Stadt"]=>
  string(9) "Riverside"
  ["Staat"]=>
  string(3) " NJ"
  ["Postleitzahl"]=>
  string(5) " 0807"
}

Open with resource: CSV file is well-formed

int(1)
array(6) {
  ["Name"]=>
  string(4) "John"
  ["Nachname"]=>
  string(3) "Doe"
  ["Anschrift"]=>
  string(17) "120 jefferson st."
  ["Stadt"]=>
  string(9) "Riverside"
  ["Staat"]=>
  string(3) " NJ"
  ["Postleitzahl"]=>
  string(6) " 08075"
}

int(2)
array(6) {
  ["Name"]=>
  string(4) "Jack"
  ["Nachname"]=>
  string(8) "McGinnis"
  ["Anschrift"]=>
  string(12) "220 hobo Av."
  ["Stadt"]=>
  string(5) "Phila"
  ["Staat"]=>
  string(3) " PA"
  ["Postleitzahl"]=>
  string(5) "09119"
}

int(3)
array(6) {
  ["Name"]=>
  string(13) "John "Da Man""
  ["Nachname"]=>
  string(6) "Repici"
  ["Anschrift"]=>
  string(17) "120 Jefferson St."
  ["Stadt"]=>
  string(9) "Riverside"
  ["Staat"]=>
  string(3) " NJ"
  ["Postleitzahl"]=>
  string(5) "08075"
}

int(4)
array(6) {
  ["Name"]=>
  string(7) "Stephen"
  ["Nachname"]=>
  string(5) "Tyler"
  ["Anschrift"]=>
  string(32) "7452 Terrace "At the Plaza" road"
  ["Stadt"]=>
  string(8) "SomeTown"
  ["Staat"]=>
  string(2) "SD"
  ["Postleitzahl"]=>
  string(6) " 91234"
}

int(5)
array(6) {
  ["Name"]=>
  string(0) ""
  ["Nachname"]=>
  string(8) "Blankman"
  ["Anschrift"]=>
  string(0) ""
  ["Stadt"]=>
  string(8) "SomeTown"
  ["Staat"]=>
  string(3) " SD"
  ["Postleitzahl"]=>
  string(6) " 00298"
}

int(6)
array(6) {
  ["Name"]=>
  string(21) "Joan "the bone", Anne"
  ["Nachname"]=>
  string(3) "Jet"
  ["Anschrift"]=>
  string(19) "9th, at Terrace plc"
  ["Stadt"]=>
  string(11) "Desert City"
  ["Staat"]=>
  string(2) "CO"
  ["Postleitzahl"]=>
  string(5) "00123"
}


read csv as assoc array

John		Doe			120 jefferson st.			Riverside
Jack		McGinnis			220 hobo Av.			Phila
John "Da Man"		Repici			120 Jefferson St.			Riverside
Stephen		Tyler			7452 Terrace "At the Plaza" road			SomeTown
		Blankman						SomeTown
Joan "the bone", Anne		Jet			9th, at Terrace plc			Desert City

get json 

string(795) "{"1":{"Name":"John","Nachname":"Doe","Anschrift":"120 jefferson st.","Stadt":"Riverside","Staat":" NJ","Postleitzahl":" 08075"},"2":{"Name":"Jack","Nachname":"McGinnis","Anschrift":"220 hobo Av.","Stadt":"Phila","Staat":" PA","Postleitzahl":"09119"},"3":{"Name":"John \"Da Man\"","Nachname":"Repici","Anschrift":"120 Jefferson St.","Stadt":"Riverside","Staat":" NJ","Postleitzahl":"08075"},"4":{"Name":"Stephen","Nachname":"Tyler","Anschrift":"7452 Terrace \"At the Plaza\" road","Stadt":"SomeTown","Staat":"SD","Postleitzahl":" 91234"},"5":{"Name":"","Nachname":"Blankman","Anschrift":"","Stadt":"SomeTown","Staat":" SD","Postleitzahl":" 00298"},"6":{"Name":"Joan \"the bone\", Anne","Nachname":"Jet","Anschrift":"9th, at Terrace plc","Stadt":"Desert City","Staat":"CO","Postleitzahl":"00123"}}"


get objects 

John		Doe			120 jefferson st.			Riverside
Jack		McGinnis			220 hobo Av.			Phila
John "Da Man"		Repici			120 Jefferson St.			Riverside
Stephen		Tyler			7452 Terrace "At the Plaza" road			SomeTown
		Blankman						SomeTown
Joan "the bone", Anne		Jet			9th, at Terrace plc			Desert City


Output from new Csv File:

"Name","Nachname","Anschrift","Stadt"
"John","Doe","120 jefferson st.","Riverside"
```

## php-csv is child class of php-file

php-csv is child class of php-file and can therefore use all methods that are also considered for php-file.
For example, all information of the file can be fetched. See more under [php-file](https://github.com/JacksonJeans/php-file).