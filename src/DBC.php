<?php
declare(strict_types=1);

namespace Wowstack\Dbc;

class DBC
{
    /**
     * DBC magic number
     */
    const SIGNATURE = 'WDBC';

    /**
     * Size of the DBC header
     */
    const HEADER_SIZE = 20;

    /**
     * pack format to read a DBC header
     */
    const HEADER_PACK_FORMAT = 'V4';

    /**
     * Number of rows contained in the file
     *
     * @var int $record_count
     */
    protected $record_count = 0;

    /**
     * Number of columns contained in the file
     *
     * @var int $field_count
     */
    protected $field_count = 0;

    /**
     * Size of each row in bytes
     *
     * @var int $record_size
     */
    protected $record_size = 0;

    /**
     * Size of the string block contained in the file
     *
     * @var int $string_block_size
     */
    protected $string_block_size = 0;

    /**
     * @var int
     */
    protected $filesize = 0;

    /**
     * @var resource
     */
    protected $filehandle = null;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var Mapping
     */
    protected $mapping = null;

    /**
     * Offset position to read from for row data
     *
     * @var int $dataOffset
     */
    protected $dataOffset = 0;

    /**
     * Offset position to read from for string data
     *
     * @var int $stringBlockOffset
     */
    protected $stringBlockOffset = 0;

    /**
     * An array of strings with the offset from string block start as index
     *
     * @var string[] $stringBlock
     */
    protected $stringBlock = null;

    /**
     * Creates a DBC reader
     *
     * @param string  $path
     * @param Mapping $map
     */
    public function __construct(string $path, Mapping $map)
    {
        $this->path = $path;

        if (!is_file($path) && !is_readable($path))
        {
            throw new DBCException('DBC file not found.');
        }

        $this->filehandle = @fopen($this->path, 'r');
        if (false === $this->filehandle) {
            throw new DBCException('DBC file is not readable.');
        }

        $this->filesize = filesize($this->path);

        if ($this->filesize < self::HEADER_SIZE)
        {
            throw new DBCException('DBC file is too small.');
        }

        $signature = @fread($this->filehandle, strlen(self::SIGNATURE));

        if (self::SIGNATURE !== $signature)
        {
            throw new DBCException('DBC file has invalid signature.');
        }

        list(, $this->record_count, $this->field_count, $this->record_size, $this->string_block_size) = unpack(self::HEADER_PACK_FORMAT, @fread($this->filehandle, self::HEADER_SIZE - strlen(self::SIGNATURE)));

        $this->dataOffset = self::HEADER_SIZE;
        $this->stringBlockOffset = self::HEADER_SIZE + ($this->record_count * $this->record_size);

        if ($this->filesize < ($this->stringBlockOffset + $this->string_block_size))
        {
            throw new DBCException('DBC file is too small.');
        }

        $this->readStringBlock();
        $this->attachMapping($map);
    }

    /**
     * Attaches and verifies a map against the opened DBC file
     *
     * @param Mapping $map
     *
     * @return DBC
     */
    public function attachMapping(Mapping $map)
    {
        $this->mapping = $map;

        return $this;
    }

    /**
     * Reads in all strings contained within the string block table
     */
    public function readStringBlock()
    {
        fseek($this->filehandle, $this->stringBlockOffset);
        $bytes_to_read = $this->string_block_size;
        $current_offset = $this->stringBlockOffset;

        $current_string =  null;
        $bytes_read = 0;
        while ($bytes_to_read > 0)
        {
            $current_byte = fread($this->filehandle, 1);
            $bytes_read++;
            if (chr(0) !== $current_byte)
            {
                $current_string = $current_string . $current_byte;
            } else {
                echo $current_string . PHP_EOL;
                if (!empty($current_string))
                {
                    $this->stringBlock[$bytes_read-strlen($current_string)] = $current_string;
                }
                $current_string = null;
            }

            $bytes_to_read--;
            $current_offset++;
        }
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getRecordCount(): int
    {
        return $this->record_count;
    }

    /**
     * @return int
     */
    public function getRecordSize(): int
    {
        return $this->record_size;
    }

    /**
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
     * Returns true if the file has a string block
     *
     * @return bool
     */
    public function hasStrings(): bool
    {
        return $this->string_block_size > 0;
    }
}
