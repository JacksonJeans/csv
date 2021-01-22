<?php

namespace JacksonJeans;

/**
 * Klasse - CreateCsvFile
 * 
 * @category    Class
 * @package     JacksonJeans
 * @author      Julian Tietz <julian.tietz@gidutex.de>
 * @license     Julian Tietz <julian.tietz@gidutex.de>
 * @version     1.0
 */
class CreateCsvFile extends CsvFile
{

    /**
     * @var array $fields 
     * - Spaltennamen der CSV
     */
    private $fields = [];

    /**
     * @var array $values
     * - Felder pro Zeile werden als Element repräsentiert
     */
    private $values = [];

    /**
     * NULL Konstrukt. Gibt nur das CreateCsvFile Objekt zurück.
     */
    public function __construct($null = null)
    {
        return $this;
    }

    /**
     * Erstelle die CSV Datei
     * 
     * @param string $destination 
     * - Zielpfad
     * @param string $delimiter
     * @param string $enclosure 
     * @param string $escape
     * @param bool $encloseAll 
     * @param bool $nullToMysqlNull
     */
    public function create($destination, $delimiter = ',', $enclosure = '"', $escape = "\\", $encloseAll = false, $nullToMysqlNull = false)
    {
        parent::__construct($this->parse($delimiter, $enclosure, $escape, $encloseAll, $nullToMysqlNull), $delimiter, $enclosure, $escape);
        $this->store($destination);
    }

    /**
     * Liefert das Array des entsprechenden Index.
     * @param int $getLine 
     * - Zeile 0...n
     * @return array $this->values[0]
     */
    public function getLine(int $index)
    {
        return $this->values[$index];
    }

    /**
     * Legt eine neue Zeile an.
     * @return void
     */
    public function setLine(array $line)
    {
        $this->values[] = $line;
    }

    /**
     * Setzt den Header
     * @param array $fields 
     * - Felder/Spaltennamen
     * @return void
     */
    public function setIndications(array $fields)
    {
        $this->setHeader($fields);
    }

    /**
     * Setzt den Header
     * @param array $fields [string, string,...n]
     * - Felder/Spaltennamen
     * @return void
     */
    public function setHeader(array $fields)
    {
        foreach ($fields as $field) {
            if (is_string($field)) {
                $this->fields[] = $field;
            }
        }
    }

    /**
     * Gibt die CSV aus den gegebenen Elementen durch die Methode setHeader()||setIndications() und setLine()
     * 
     * @param string $delimiter
     * @param string $enclosure 
     * @param string $escape
     * @param bool $encloseAll 
     * @param bool $nullToMysqlNull
     */
    public function get($delimiter = ',', $enclosure = '"', $escape = "\\", $encloseAll = false, $nullToMysqlNull = false)
    {
        return $this->parse($delimiter, $enclosure, $escape, $encloseAll, $nullToMysqlNull);
    }

    /**
     * toString Methode
     */
    public function __toString()
    {
        return $this->string;
    }

    /**
     * Parst eine CSV Datei aus den Eingaben
     * 
     * @param string $delimiter
     * @param string $enclosure 
     * @param string $escape
     * @param bool $encloseAll 
     * @param bool $nullToMysqlNull
     */
    public function &parse($delimiter = ',', $enclosure = '"', $escape = "\\", $encloseAll = false, $nullToMysqlNull = false)
    {
        $string = '';
        if (count($this->fields) > 0) {
            $fields = parent::ArrayToCsvString($this->fields, $delimiter, $enclosure, $escape, $encloseAll, $nullToMysqlNull);

            $string .= $fields . "\n";
        }

        if (count($this->values) > 0) {
            foreach ($this->values as $value) {
                $string .= parent::ArrayToCsvString($value, $delimiter, $enclosure, $escape, $encloseAll, $nullToMysqlNull) . "\n";
            }
        }

        $this->string = $string;
        return $this->string;
    }
}
