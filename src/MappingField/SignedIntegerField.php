<?php

declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

/**
 * Implements a field containing signed integer value.
 */
class SignedIntegerField extends AbstractField implements MappingFieldInterface
{
    /**
     * Name of type.
     */
    const TYPE = 'int';

    /**
     * {@inheritdoc}
     */
    protected $size = 4;

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
    const PACK_FORMAT = 'l';

    /**
     * Constructs the field.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @throws MappingException
     */
    public function __construct(string $name, array $parameters = [])
    {
        $this->name = $name;
        $this->setParameters($parameters);
        $this->setOptionalParameters($parameters);
    }
}
