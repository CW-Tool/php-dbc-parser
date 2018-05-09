<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

/**
 * Implements a single record within a DBC file.
 */
class DBCRecord
{
    /**
     * @var DBC
     */
    protected $dbcFile = null;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $recordOffset = 0;

    /**
     * @var int
     */
    protected $recordSize = 0;

    /**
     * @var resource
     */
    protected $fileHandle = null;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @var int
     */
    protected $identifier = 0;

    /**
     * Constructs a new DBC row.
     *
     * @param DBC $dbcFile
     * @param int $position
     */
    public function __construct(DBC $dbcFile, int $position)
    {
        $this->dbcFile = $dbcFile;
        $this->position = $position;

        $this->recordSize = $this->dbcFile->getRecordSize();
        $this->recordOffset = DBC::HEADER_SIZE + $this->position * $this->recordSize;
        $this->fileHandle = $this->dbcFile->getFileHandle();

        fseek($this->fileHandle, $this->recordOffset);
        if ($this->recordSize > 0) {
            $this->data = fread($this->fileHandle, $this->recordSize);
        }
    }

    /**
     * Reads the current row into key/value array.
     *
     * @return array
     *
     * @throws DBCException
     */
    public function read(): array
    {
        $map = $this->dbcFile->getMap();
        if (empty($map)) {
            throw new DBCException('DBCRecord can not be read without a mapping');
        }

        $data = null;
        $format = null;
        $strings = [];
        $foreignKeys = [];
        $fields = $map->getParsedFields();

        foreach ($fields as $fieldName => $fieldData) {
            $format[] = $fieldData['format'];
            if ('string' === $fieldData['type'] || 'localized_string' === $fieldData['type']) {
                $strings[] = $fieldName;
            }
            if ('foreign_key' === $fieldData['type']) {
                $foreignKeys[] = $fieldName;
            }
        }

        $format = implode('/', $format);
        $data = unpack($format, $this->data);

        // This ensure that string fields will be empty strings instead of 0.
        foreach ($strings as $string) {
            $stringPointer = $data[$string];
            if ($data[$string] > 0) {
                try {
                    $data[$string] = $this->dbcFile->getString($data[$string]);
                } catch (DBCException $dbcException) {
                    $data[$string] = '';
                    $this->dbcFile->addError('string', $this->position, $string, 'String pointer not found at offset '.$stringPointer);
                }
            } else {
                $data[$string] = '';
            }
        }

        // This ensures fields containing references to other tables will be nulled
        // if they do not contain a valid reference.
        foreach ($foreignKeys as $foreignKey) {
            if ($data[$foreignKey] <= 0) {
                $data[$foreignKey] = null;
            }
        }

        $data = array_filter($data, function ($key) {
            return false === strpos($key, '_checksum') && false === strpos($key, '_unused');
        }, ARRAY_FILTER_USE_KEY);

        return $data;
    }
}
