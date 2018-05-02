<?php

declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

/**
 * Defines an interface for all mapping field types to implement.
 */
interface MappingFieldInterface
{
    /**
     * Provides the name of the field.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Provides the type of the field.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Provides how many bytes this field requires.
     *
     * @return int
     */
    public function getSize(): int;

    /**
     * Provides how many of this field follow.
     *
     * @return int
     */
    public function getCount(): int;

    /**
     * Provides how many of this field follow.
     *
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * Provides how many bytes for all fields follow.
     *
     * @return int
     */
    public function getTotalSize(): int;

    /**
     * Returns the offset at which to find the data.
     *
     * @return int
     */
    public function getOffset(): int;

    /**
     * Returns the resulting field(s).
     *
     * @return array
     */
    public function getParsedFields(): array;
}
