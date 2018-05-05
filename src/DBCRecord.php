<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

class DBCRecord
{
    /**
     * @var DBC
     */
    protected $dbc_file = null;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var int
     */
    protected $record_offset = 0;

    /**
     * @var int
     */
    protected $record_size = 0;

    /**
     * @var resource
     */
    protected $file_handle = null;

    /**
     * @var mixed
     */
    protected $data = null;

    /**
     * @var int
     */
    protected $id = 0;

    /**
     * Constructs a new DBC row.
     *
     * @param DBC $dbc_file
     * @param int $position
     */
    public function __construct(DBC $dbc_file, int $position)
    {
        $this->dbc_file = $dbc_file;
        $this->position = $position;

        $this->record_size = $this->dbc_file->getRecordSize();
        $this->record_offset = DBC::HEADER_SIZE + $this->position * $this->record_size;
        $this->file_handle = $this->dbc_file->getFileHandle();

        fseek($this->file_handle, $this->record_offset);
        if ($this->record_size > 0) {
            $this->data = fread($this->file_handle, $this->record_size);
        }
    }

    /**
     * Reads the current row into key/value array.
     *
     * @return array
     */
    public function read(): array
    {
        $map = $this->dbc_file->getMap();
        if (empty($map)) {
            throw new DBCException('DBCRecord can not be read without a mapping');
        }

        $data = [];
        $format = [];
        $strings = [];
        $foreign_keys = [];
        $fields = $map->getParsedFields();

        foreach ($fields as $field_name => $field_data) {
            $format[] = $field_data['format'];
            if ('string' === $field_data['type'] || 'localized_string' === $field_data['type']) {
                $strings[] = $field_name;
            }
            if ('foreign_key' === $field_data['type']) {
                $foreign_keys[] = $field_name;
            }
        }

        $format = implode('/', $format);
        $data = unpack($format, $this->data);

        // This ensure that string fields will be empty strings instead of 0.
        foreach ($strings as $string) {
            $string_pointer = $data[$string];
            if ($data[$string] > 0) {
                try {
                    $data[$string] = $this->dbc_file->getString($data[$string]);
                } catch (DBCException $dbc_exception) {
                    $data[$string] = '';
                    $this->dbc_file->addError('string', $this->position, $string, 'String pointer not found at offset '.$string_pointer);
                }
            } else {
                $data[$string] = '';
            }
        }

        // This ensures fields containing references to other tables will be nulled
        // if they do not contain a valid reference.
        foreach ($foreign_keys as $foreign_key) {
            if ($data[$foreign_key] <= 0) {
                $data[$foreign_key] = null;
            }
        }

        $data = array_filter($data, function ($key) {
            return false === strpos($key, '_checksum') && false === strpos($key, '_unused');
        }, ARRAY_FILTER_USE_KEY);

        return $data;
    }
}
