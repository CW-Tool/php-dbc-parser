<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

class DBCIterator implements \Iterator
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
     * Creates a new DBC iterator.
     *
     * @var DBC
     */
    public function __construct(DBC $dbc_file)
    {
        $this->dbc_file = $dbc_file;
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
     */
    public function current()
    {
        return $this->dbc_file->getRecord($this->position);
    }

    /**
     * Returns the current record index.
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

    public function prev()
    {
        --$this->position;
    }

    /**
     * Seeks to given position.
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
        return $this->dbc_file->hasRecord($this->position);
    }
}
