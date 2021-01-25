<?php

namespace JacksonJeans;

/**
 * Klasse - CsvFile 
 * 
 * @category    Class
 * @package     JacksonJeans
 * @author      Julian Tietz <julian.tietz@gidutex.de>
 * @license     Julian Tietz <julian.tietz@gidutex.de>
 * @version     1.0
 */
class CsvFile extends File
{
    /**
     * @var string $string
     * - CSV String
     */
    public $string;

    /**
     * @var bool $bool
     * - CSV valide 
     */
    public $bool;

    /**
     * @var string $Delimiter
     * - Delimiter
     */
    public $Delimiter;

    /**
     * @var string $Enclosure
     * - Enclosure
     */
    public $Enclosure;

    /**
     * @var string $Escape
     * - Escape
     */
    public $Escape;

    /**
     * @var bool $Error 
     */
    public $Error = false;

    /**
     * @var array $ErrorArr
     */
    public $ErrorArr = [];

    /**
     * @var string $ErrorMsg
     */
    public $ErrorMsg = '';

    /**
     * @var bool $_new
     * - Indikator ob es eine neue CSV Datei ist.
     */
    private $_new = false;

    /**
     * @var int $_line_position 
     * - Aktuelle Zeile der CSV 
     */
    private $_line_position = 0;

    /**
     * @var int $headerLine
     * - Zeile der Spaltennamen
     */
    private $headerLine = 0;

    /**
     * Konstruiert das CsvFile Objekt.
     * @param string|resource $csvFile CallByReference
     * - Wenn String und Dateipfad, dann wird die Datei geladen.
     * - Wenn Resource, dann wird diese Resource ausgelesen.
     * @param string $delimiter 
     * - Delimiter 
     * @param string $enclosure 
     * - Enclosure
     * @param string $escape 
     * - Escape
     * @return JacksonJeans\CsvFile $this
     * - Liefert das CsvFile Objekt 
     * @return bool $return 
     * - Wenn String und die Daten nicht well-formed sind, dann gibt $return FALSE zurück.
     */
    public function __construct(&$csvFile, $delimiter = ',', $enclosure = '"', $escape = "\\")
    {
        $this->Delimiter = $delimiter;
        $this->Enclosure = $enclosure;
        $this->Escape = $escape;

        $t = gettype($csvFile);
        switch ($t) {
            case 'string':
                # File
                if (FileInfo::isFile($csvFile)) {
                    $this->setFile($csvFile);
                    $this->string = $this->read();
                    # Prüfe obs CSV ist.
                } else {

                    if ($this->isWellFormed($csvFile, $delimiter, $enclosure, $escape)) {
                        parent::__construct('tempFile', 'csv', false);
                        $this->write($csvFile);
                        $this->_new = true;
                        $this->string = $csvFile;
                    }
                }
                break;
            default:
                if (is_resource($csvFile)) {
                    $this->setResource($csvFile);
                    $this->string = $this->read();
                } else {
                    $t = gettype($csvFile);
                    throw new \InvalidArgumentException("Argument ist keine resource oder string. {$t} wurde anstelle übergeben.");
                }
                break;
        }

        return $this;
    }

    /**
     * Gibt ein neues CreateCsvFile Objekt zurück
     */
    public function new()
    {
        return new CreateCsvFile;
    }

    /**
     * Legt die Zeile fest, in der die Spaltennamen hinterlegt sind.
     * @var int $headerLine 
     * - Zeile der Spaltennamen
     */
    public function setHeaderLine(int $headerLine)
    {
        if ($headerLine >= 0) {
            $this->headerLine = $headerLine;
            return $this->headerLine;
        } else {
            return false;
        }
    }

    /**
     * Liefert die aktuelle Zeile des Dateizeigers 
     * @return int $_line_position
     */
    public function getLinePointer()
    {
        return $this->_line_position;
    }

    /**
     * Setzt die aktuelle Zeile des Dateizeigers
     */
    public function setLinePointer(int $line)
    {
        $this->setPointer($line);
        $this->_line_position;
    }

    /**
     * Ließt eine Zeile zu einem Array
     * 
     * @param bool $assoc 
     * - Legt fest ob die Rückgabe ein assoziatives oder numerisch indexiertes Array ist.
     */
    public function readLineToArray($assoc = true, $fields = null)
    {
        $error = 0;
        $errorArr = [];
        if (!$assoc) {
            return str_getcsv($this->readLine(), $this->Delimiter, $this->Enclosure, $this->Escape);
            $this->_line_position++;
        } elseif (is_array($fields)) {
            $array = [];
            $row = str_getcsv($this->readLine(), $this->Delimiter, $this->Enclosure, $this->Escape);
            if ($this->getLastLine() === false) {
                return $array;
            }

            if (($row !== false) && (($row[0] == null) && (count($row) < 2))) {
                $error++;
                $errorArr[] = $this->getLastLine();
                $this->ErrorArr = $errorArr;
                $this->Error = true;
                $this->ErrorMsg = 'Daten konnten nicht geparst werden';
                return $array;
            } else {
                foreach ($row as $k => $value) {
                    $array[$fields[$k]] = $value;
                }
                $this->_line_position++;
                return $array;
            }
        } else {
            return false;
        }
    }

    /**
     * Parst den Inhalt einer CSV Datei in ein Array und gibt dieses zurück.
     * @return array $array
     * - Gibt ein assoziatives Array zurück.
     */
    public function toArray($nullToMysqlNull = false)
    {
        #if ($this->file !== null) {
            $lines = $fields = array();
            $i = 0;
            $error = 0;
            $errorArr = [];
            $row = true;
            $this->resetPointer();

            $fields = str_getcsv($this->readLine($this->headerLine), $this->Delimiter, $this->Enclosure, $this->Escape);
            if (!empty($fields)) {
                while ($row !== false) {
                    $row = str_getcsv($this->readLine(), $this->Delimiter, $this->Enclosure, $this->Escape);
                    if ($i == $this->headerLine) {
                        $i++;
                        continue;
                    }
                    if ($this->getLastLine() === false) {
                        $row = false;
                    }

                    # Falls $row !== false, $row[0] = null ist und nicht mehr Elemente hat, 
                    # dann ist beim parsen etwas schief gelaufen.
                    if (($row !== false) && (($row[0] == null) && (count($row) < 2))) {
                        $error++;
                        $errorArr[] = $this->getLastLine();
                        continue;
                    }

                    if (is_array($row)) {
                        foreach ($row as $k => $value) {
                            if (($nullToMysqlNull) && ($value == '')) {
                                $value = 'NULL';
                            }
                            $lines[$i][$fields[$k]] = $value;
                        }


                        $i++;
                    } else {
                        $row = false;
                    }
                }
            }

            if (count($errorArr) > 0) {
                $this->ErrorArr = $errorArr;
                $this->Error = true;
                $this->ErrorMsg = 'Daten konnten nicht geparst werden';
            }
            $this->resetPointer();
            return $lines;
        #} else {
            # EXCEPTION Keine Datei gesetzt
        #}
    }

    /**
     * Prüft ob die Datei parsbar ist.
     * 
     */
    public function isWellFormed($line = null, $delimiter = ",", $enclosure = '"', $escape = "\\")
    {
        if ((is_null($line)) && ($this->file !== null)) {
            $str = str_getcsv($this->readLine(), $this->Delimiter, $this->Enclosure, $this->Escape);
            return (($str) && $this->resetPointer()) ? true : false;
        } else {
            $str = str_getcsv($line, $delimiter, $enclosure, $escape);
            return ($str) ? true : false;
        }
    }

    /**
     * Parst die CSV in ein Array mit Objekten
     * @return array $object [stdClass, stdClass,...]
     * - Array mit Objekten als Elementes
     */
    public function toObject()
    {
        if ($this->isWellFormed()) {
            $object = [];
            foreach ($this->toArray() as $line) {
                $object[] = json_decode(json_encode($line), false, 512, JSON_FORCE_OBJECT);
            }
            return $object;
        }
    }

    /**
     * Parst die CSV in ein JSON
     */
    public function toJson()
    {
        if ($this->isWellFormed()) {
            return json_encode($this->toArray());
        }
    }

    /**
     * @var string $delimiter 
     */
    public function setDelimiter(string $delimiter)
    {
        $this->Delimiter = $delimiter;
    }

    /**
     * @var string $enclosure
     */
    public function setEnclosure(string $enclosure)
    {
        $this->Enclosure = $enclosure;
    }

    /**
     * @var string $escape
     */
    public function setEscape(string $escape)
    {
        $this->Escape = $escape;
    }

    /**
     * Speichert das Ergebnis
     * @return bool $result
     */
    public function store($destination = null)
    {
        if (($this->_new) && (is_string($destination))) {
            # verschiebe von temp zum Zielverzeichnis
            return $this->move($destination);
        } else {
            # Kopiere ins Zielverzeichnis
            if (is_string($destination)) {
                return $this->copy($destination);
                # schließt die Datei
            } else {
                $this->close();
            }
        }
    }


    /**
     * toString Methode 
     * @return string $this->string
     */
    public function __toString()
    {
        return $this->string;
    }

    /**
     * Parst eine Zeile[string] in das CSV Format und maskiert alle delimiter und enclosure Zeichen.
     * 
     * @param array &$fields
     * - Array zum parsen in das CSV Format
     * @param string $delimtier 
     * @param string $enclosure 
     * @param bool $encloseAll
     * - Maskiere alle Felder
     * @param bool $nullToMysqlNull 
     * - Maskiert NULL Werte in das MySQL NULL Format.
     * @return string|bool $output
     * - Gibt [string] $output aus bei Erfolg, im Fehlerfall [bool] FALSE.
     */
    public static function ArrayToCsvString(array &$fields, $delimiter = ';', $enclosure = '"', $escape = "\\", $encloseAll = false, $nullToMysqlNull = false)
    {
        $delimiter_esc = preg_quote($delimiter, '/');
        $enclosure_esc = preg_quote($enclosure, '/');

        $output = array();
        foreach ($fields as $field) {
            if ($field === null && $nullToMysqlNull) {
                $output[] = 'NULL';
                continue;
            }

            if ($encloseAll || preg_match("/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field)) {
                $output[] = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
            } else {
                $output[] = $field;
            }
        }

        return implode($delimiter, $output);
    }
}
