<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

class DBC implements \IteratorAggregate
{
    /**
     * DBC magic number.
     */
    const SIGNATURE = 'WDBC';

    /**
     * Size of the DBC header.
     */
    const HEADER_SIZE = 20;

    /**
     * pack format to read a DBC header.
     */
    const HEADER_PACK_FORMAT = 'V4';

    /**
     * Number of rows contained in the file.
     *
     * @var int
     */
    protected $record_count = 0;

    /**
     * Number of columns contained in the file.
     *
     * @var int
     */
    protected $field_count = 0;

    /**
     * Size of each row in bytes.
     *
     * @var int
     */
    protected $record_size = 0;

    /**
     * Size of the string block contained in the file.
     *
     * @var int
     */
    protected $string_block_size = 0;

    /**
     * @var int
     */
    protected $file_size = 0;

    /**
     * @var resource
     */
    protected $file_handle = null;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var Mapping
     */
    protected $mapping = null;

    /**
     * Offset position to read from for row data.
     *
     * @var int
     */
    protected $dataOffset = 0;

    /**
     * Offset position to read from for string data.
     *
     * @var int
     */
    protected $stringBlockOffset = 0;

    /**
     * An array of strings with the offset from string block start as index.
     *
     * @var string[]
     */
    protected $stringBlock = null;

    /**
     * List of errors occuring while reading data from the DBC file.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Creates a DBC reader.
     *
     * @param string  $path
     * @param Mapping $map
     *
     * @throws DBCException
     */
    public function __construct(string $path, Mapping $map = null)
    {
        $this->path = $path;

        if (!is_file($path) && !is_readable($path)) {
            throw new DBCException('DBC file not found.');
        }

        $this->file_handle = @fopen($this->path, 'r');
        if (false === $this->file_handle) {
            throw new DBCException('DBC file is not readable.');
        }

        $this->file_size = filesize($this->path);

        if ($this->file_size < self::HEADER_SIZE) {
            throw new DBCException('DBC file is too small.');
        }

        $signature = @fread($this->file_handle, strlen(self::SIGNATURE));

        if (self::SIGNATURE !== $signature) {
            throw new DBCException('DBC file has invalid signature.');
        }

        list(, $this->record_count, $this->field_count, $this->record_size, $this->string_block_size) = unpack(self::HEADER_PACK_FORMAT, @fread($this->file_handle, self::HEADER_SIZE - strlen(self::SIGNATURE)));

        $this->dataOffset = self::HEADER_SIZE;
        $this->stringBlockOffset = self::HEADER_SIZE + ($this->record_count * $this->record_size);

        if ($this->file_size < ($this->stringBlockOffset + $this->string_block_size)) {
            throw new DBCException('DBC file is too small.');
        }

        $this->readStringBlock();
        $this->attachMapping($map);
    }

    /**
     * Attaches and verifies a map against the opened DBC file.
     *
     * @param Mapping $map
     *
     * @return DBC
     *
     * @throws DBCException
     */
    public function attachMapping(Mapping $map = null)
    {
        $this->mapping = $map;

        if (null !== $this->mapping) {
            $delta = $this->mapping->getFieldCount() - $this->getFieldCount();
            if (0 !== $delta) {
                throw new DBCException('Mapping holds '.$this->mapping->getFieldCount().' fields but DBC holds '.$this->getFieldCount().' fields.');
            }

            if ($this->mapping->hasStrings() != $this->hasStrings()) {
                throw new DBCException('No strings attached! Mapping says '.$this->mapping->hasStrings().', DBC says '.$this->hasStrings());
            }
        }

        return $this;
    }

    /**
     * Reads in all strings contained within the string block table.
     */
    public function readStringBlock()
    {
        if ($this->string_block_size > 1) {
            fseek($this->file_handle, $this->stringBlockOffset);
            $bytes_to_read = $this->string_block_size;
            $current_offset = $this->stringBlockOffset;

            $current_string = null;
            $bytes_read = 0;
            while ($bytes_to_read > 0) {
                $current_byte = fread($this->file_handle, 1);
                ++$bytes_read;
                if (chr(0) !== $current_byte) {
                    $current_string = $current_string.$current_byte;
                } else {
                    if (!empty($current_string)) {
                        $this->stringBlock[$bytes_read - strlen($current_string)] = $current_string;
                    }
                    $current_string = null;
                }

                --$bytes_to_read;
                ++$current_offset;
            }
        } else {
            $this->stringBlock = [];
        }
    }

    /**
     * Returns the filename of the Mapping.
     *
     * @return string
     */
    public function getName(): string
    {
        return pathinfo($this->getPath())['filename'];
    }

    /**
     * Returns the canonical path to the file.
     *
     * @return string
     */
    public function getPath(): string
    {
        return realpath($this->path);
    }

    /**
     * Returns the number of rows in the file.
     *
     * @return int
     */
    public function getRecordCount(): int
    {
        return $this->record_count;
    }

    /**
     * Returns the number of bytes per row.
     *
     * @return int
     */
    public function getRecordSize(): int
    {
        return $this->record_size;
    }

    /**
     * Returns the actual amount of colums in the file.
     *
     * @return int
     */
    public function getFieldCount(): int
    {
        return $this->field_count;
    }

    /**
     * @return int
     */
    public function getStringBlockSize(): int
    {
        return $this->string_block_size;
    }

    /**
     * Returns true if the file has a string block.
     *
     * @return bool
     */
    public function hasStrings(): bool
    {
        return count($this->stringBlock) > 0;
    }

    /**
     * Returns all strings found within the file.
     *
     * @return array
     */
    public function getStringBlock(): array
    {
        return $this->stringBlock;
    }

    /**
     * Returns the string from the given offset.
     *
     * @return string
     *
     * @throws DBCException
     */
    public function getString(int $offset): string
    {
        if (array_key_exists($offset + 1, $this->stringBlock)) {
            return $this->stringBlock[$offset + 1];
        }

        throw new DBCException('DBC String Entry not found at index '.($offset + 1));
    }

    /**
     * Returns the mapping attach to the DBC.
     *
     * @return Mapping
     */
    public function getMap()
    {
        return $this->mapping;
    }

    /**
     * Returns the handle to the associated file.
     *
     * @return resource
     */
    public function getFileHandle()
    {
        return $this->file_handle;
    }

    /**
     * Provides an iterator to iterate over the DBC records.
     *
     * @return DBCIterator
     */
    public function getIterator(): DBCIterator
    {
        return new DBCIterator($this);
    }

    /**
     * Returns information if a given record index exists.
     *
     * @param int $position
     */
    public function hasRecord(int $position): bool
    {
        return $position >= 0 && $position < $this->record_count;
    }

    /**
     * Returns the record using the given index.
     *
     * @param int $position
     *
     * @return DBCRecord
     *
     * @throws DBCException
     */
    public function getRecord(int $position): DBCRecord
    {
        if ($this->hasRecord($position)) {
            return new DBCRecord($this, $position);
        }

        throw new DBCException('DBC Record not found.');
    }

    /**
     * Appends an error to the list.
     *
     * @param string $type
     * @param int    $position
     * @param string $field
     * @param string $hint
     */
    public function addError(string $type, int $position, string $field, string $hint = '')
    {
        $this->errors[] = [
            'type' => $type,
            'field' => $field,
            'record' => $position,
            'hint' => $hint,
        ];
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
