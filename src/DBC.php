<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

use Doctrine\Common\Inflector\Inflector;

/**
 * Implements a DBC database file.
 */
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
    protected $recordCount = 0;

    /**
     * Number of columns contained in the file.
     *
     * @var int
     */
    protected $fieldCount = 0;

    /**
     * Size of each row in bytes.
     *
     * @var int
     */
    protected $recordSize = 0;

    /**
     * Size of the string block contained in the file.
     *
     * @var int
     */
    protected $stringBlockSize = 0;

    /**
     * @var int
     */
    protected $fileSize = 0;

    /**
     * @var resource|bool
     */
    protected $fileHandle = null;

    /**
     * @var string
     */
    protected $filePath = '';

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
        $this->filePath = $path;

        if (!is_file($path) && !is_readable($path)) {
            throw new DBCException('DBC file not found.');
        }

        $this->fileHandle = fopen($this->filePath, 'r');
        if (false === $this->fileHandle) {
            throw new DBCException('DBC file is not readable.');
        }

        $this->fileSize = filesize($this->filePath);

        if ($this->fileSize < self::HEADER_SIZE) {
            throw new DBCException('DBC file is too small.');
        }

        $signature = fread($this->fileHandle, strlen(self::SIGNATURE));

        if (self::SIGNATURE !== $signature) {
            throw new DBCException('DBC file has invalid signature.');
        }

        list(, $this->recordCount, $this->fieldCount, $this->recordSize, $this->stringBlockSize) = unpack(self::HEADER_PACK_FORMAT, fread($this->fileHandle, self::HEADER_SIZE - strlen(self::SIGNATURE)));

        $this->dataOffset = self::HEADER_SIZE;
        $this->stringBlockOffset = self::HEADER_SIZE + ($this->recordCount * $this->recordSize);

        if ($this->fileSize < ($this->stringBlockOffset + $this->stringBlockSize)) {
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
                throw new DBCException(
                    sprintf('Mapping holds %u fields but DBC holds %u fields.', $this->mapping->getFieldCount(), $this->getFieldCount())
                );
            }

            if ($this->mapping->hasStrings() !== $this->hasStrings()) {
                throw new DBCException(
                    sprintf('No strings attached! Mapping says %s, DBC says %s.', $this->mapping->hasStrings(), $this->hasStrings())
                );
            }
        }

        return $this;
    }

    /**
     * Reads in all strings contained within the string block table.
     */
    public function readStringBlock()
    {
        if ($this->stringBlockSize > 1) {
            fseek($this->fileHandle, $this->stringBlockOffset);
            $bytesToRead = $this->stringBlockSize;
            $currentOffset = $this->stringBlockOffset;

            $currentString = null;
            $bytesRead = 0;
            while ($bytesToRead > 0) {
                $currentByte = fread($this->fileHandle, 1);
                ++$bytesRead;
                if (chr(0) !== $currentByte) {
                    $currentString = $currentString.$currentByte;
                } else {
                    if (!empty($currentString)) {
                        $this->stringBlock[$bytesRead - strlen($currentString)] = $currentString;
                    }
                    $currentString = null;
                }

                --$bytesToRead;
                ++$currentOffset;
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
        return Inflector::singularize(pathinfo($this->getFilePath())['filename']);
    }

    /**
     * Returns the canonical path to the file.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return realpath($this->filePath);
    }

    /**
     * Returns the number of rows in the file.
     *
     * @return int
     */
    public function getRecordCount(): int
    {
        return $this->recordCount;
    }

    /**
     * Returns the number of bytes per row.
     *
     * @return int
     */
    public function getRecordSize(): int
    {
        return $this->recordSize;
    }

    /**
     * Returns the actual amount of colums in the file.
     *
     * @return int
     */
    public function getFieldCount(): int
    {
        return $this->fieldCount;
    }

    /**
     * @return int
     */
    public function getStringBlockSize(): int
    {
        return $this->stringBlockSize;
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
     * @param int $offset offset in bytes
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

        throw new DBCException(sprintf('DBC String Entry not found at index %u', ($offset + 1)));
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
        return $this->fileHandle;
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
     *
     * @return bool
     */
    public function hasRecord(int $position): bool
    {
        return $position >= 0 && $position < $this->recordCount;
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
