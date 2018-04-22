<?php
declare(strict_types=1);

namespace Wowstack\Dbc;

use Symfony\Component\Yaml\Yaml;

class Mapping
{
    /**
     * @var array
     */
    private $_mapping = [];

    /**
     * Create an instance
     *
     * @param [] $mapping
     */
    public function __construct($mapping = [])
    {
        $this->_mapping = $mapping;
    }

    /**
     * Create an instance with a mapping from file
     *
     * @param string $yaml path to YAML file
     * @return Mapping
     */
    public static function fromYAML($yaml): Mapping
    {
        return new self(Yaml::parseFile($yaml));
    }
}
