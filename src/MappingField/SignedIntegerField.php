<?php
declare(strict_types=1);

namespace Wowstack\Dbc\MappingField;

class SignedIntegerField extends AbstractField implements MappingFieldInterface
{
    /**
     * Name of type
     */
    const TYPE =  'int';

    /**
     * @inheritDoc
     */
    protected $size = 4;

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
     * Constructs the field
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct($name, $parameters = [])
    {
        $this->name = $name;
        $this->setParameters($parameters);
        $this->setOptionalParameters($parameters);
    }
}
