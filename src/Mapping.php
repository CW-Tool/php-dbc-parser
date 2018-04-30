<?php
declare(strict_types=1);

namespace Wowstack\Dbc;

use Symfony\Component\Yaml\Yaml;

class Mapping
{

    /**
     * @var array
     */
    protected $_settings = [];

    /**
     * @var array
     */
    protected $_fields = [];

    /**
     * @var int
     */
    protected $_fieldCount = 0;

    /**
     * Create an instance
     *
     * @param [] $mapping
     */
    public function __construct(array $mapping = [])
    {
        $this->_settings = isset($mapping['settings']) ? $mapping['settings'] : [];
        $this->_fields = isset($mapping['fields']) ? $mapping['fields'] : [];
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
