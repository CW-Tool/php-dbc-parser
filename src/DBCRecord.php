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
        $fields = $map->getParsedFields();

        foreach ($fields as $field_name => $field_data) {
            $format[] = $field_data['format'];
            if ('string' === $field_data['type'] || 'localized_string' === $field_data['type']) {
                $strings[] = $field_name;
            }
        }

        $format = implode('/', $format);
        $data = unpack($format, $this->data);

        foreach ($strings as $string) {
            if ($data[$string] > 0) {
                $data[$string] = $this->dbc_file->getString($data[$string]);
            }
        }

        return $data;
    }
}
