<?php
declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

abstract class AbstractField
{
    /**
     * Size of field in bytes
     */
    protected $size = 0;

    /**
     * Amount of fields to follow
     *
     * @var int
     */
    protected $count = 0;

    /**
     * Defines required parameters
     */
    const PARAMETERS = ['count'];

    /**
     * Defines optional parameters and their defaults
     */
    const OPTIONAL_PARAMETERS = [];

    /**
     * Sets required parameters
     *
     * @param array $parameters
     */
    public function setParameters($parameters = [])
    {
        foreach ($this::PARAMETERS as $key)
        {
            if (!array_key_exists($key, $parameters))
            {
                throw new MappingException("Parameter ${key} missing.");
            }

            $this->{$key} = $parameters[$key];
        }
    }

    /**
     * Sets optional parameters
     *
     * @param array $parameters
     */
    public function setOptionalParameters($parameters = [])
    {
        foreach ($this::OPTIONAL_PARAMETERS as $key => $default)
        {
            if (array_key_exists($key, $parameters))
            {
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
     * Provides how many bytes this field requires
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
     * Provides how many bytes for all fields follow.
     *
     * @return int
     */
    public function getTotalSize(): int
    {
        return $this->count * $this->size;
    }
}
