<?php

declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

abstract class AbstractField
{
    /**
     * Size of field in bytes.
     */
    protected $size = 0;

    /**
     * Amount of fields to follow.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Defines required parameters.
     */
    const PARAMETERS = ['count'];

    /**
     * Defines optional parameters and their defaults.
     */
    const OPTIONAL_PARAMETERS = [];

    /**
     * Format used to pack/unpack this field type.
     */
    const PACK_FORMAT = '';

    /**
     * Sets required parameters.
     *
     * @param array $parameters
     *
     * @throws MappingException
     */
    public function setParameters(array $parameters = [])
    {
        foreach ($this::PARAMETERS as $key) {
            if (!array_key_exists($key, $parameters)) {
                throw new MappingException("Parameter ${key} missing.");
            }

            $this->{$key} = $parameters[$key];
        }
    }

    /**
     * Sets optional parameters.
     *
     * @param array $parameters
     */
    public function setOptionalParameters(array $parameters = [])
    {
        foreach ($this::OPTIONAL_PARAMETERS as $key => $default) {
            if (array_key_exists($key, $parameters)) {
                $this->{$key} = $parameters[$key];
            } else {
                $this->{$key} = $default;
            }
        }
    }

    /**
     * Provides the name of the field.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Provides the type of the field.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this::TYPE;
    }

    /**
     * Provides how many bytes this field requires.
     *
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Provides how many of this field follow.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Provides how many of this field follow.
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->count;
    }

    /**
     * Provides how many bytes for all fields follow.
     *
     * @return int
     */
    public function getTotalSize(): int
    {
        return $this->count * $this->size;
    }

    /**
     * Returns the offset at which to find the data.
     *
     * @return int
     */
    public function getOffset(): int
    {
        return 0;
    }

    /**
     * Returns the resulting field(s).
     *
     * @return array
     */
    public function getParsedFields(): array
    {
        $count = 1;
        $parsed_fields = [];

        while ($count <= $this->getCount()) {
            $field_name = ($this->getCount() > 1 ? $this->getName().$count : $this->getName());
            $parsed_field = [
                'type' => $this->getType(),
                'size' => $this->getSize(),
                'format' => $this::PACK_FORMAT.'1'.$field_name,
                'offset' => $this->getOffset(),
            ];

            $parsed_fields[$field_name] = $parsed_field;
            ++$count;
        }

        return $parsed_fields;
    }
}
