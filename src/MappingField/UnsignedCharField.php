<?php

declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

class UnsignedCharField extends AbstractField implements MappingFieldInterface
{
    /**
     * Name of type.
     */
    const TYPE = 'uchar';

    /**
     * {@inheritdoc}
     */
    protected $size = 1;

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
    const PACK_FORMAT = 'C';

    /**
     * Constructs the field.
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct(string $name, array $parameters = [])
    {
        $this->name = $name;
        $this->setParameters($parameters);
        $this->setOptionalParameters($parameters);
    }
}
