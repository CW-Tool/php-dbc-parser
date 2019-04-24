<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

/**
 * Implements iteration over the records of a DBC file.
 */
class DBCIterator implements \Iterator
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
     * Creates a new DBC iterator.
     *
     * @var DBC $dbcFile A valid DBC file to be used
     */
    public function __construct(DBC $dbcFile)
    {
        $this->dbcFile = $dbcFile;
    }

    /**
     * Reset iterator to the first record.
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Returns the current record.
     *
     * @return DBCRecord
     *
     * @throws DBCException
     */
    public function current()
    {
        return $this->dbcFile->getRecord($this->position);
    }

    /**
     * Returns the current record index.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Advances the record index by one.
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * Moves the record index back by one.
     */
    public function prev()
    {
        --$this->position;
    }

    /**
     * Moves record position to given position.
     *
     * @param int $position
     */
    public function seek(int $position)
    {
        $this->position = $position;
    }

    /**
     * Returns information if a given record index exists.
     *
     * @return bool
     */
    public function valid(): bool
    {
        return $this->dbcFile->hasRecord($this->position);
    }
}
