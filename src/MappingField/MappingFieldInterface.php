<?php
declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

/**
 * Defines an interface for all mapping field types to implement.
 */
interface MappingFieldInterface {
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
     * Provides how many bytes this field requires
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
     * Provides how many bytes for all fields follow.
     *
     * @return int
     */
    public function getTotalsize(): int;
}
