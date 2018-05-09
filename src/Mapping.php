<?php

declare(strict_types=1);

namespace Wowstack\Dbc;

use Symfony\Component\Yaml\Yaml;
use Wowstack\Dbc\MappingField as Mappings;
use Wowstack\Dbc\MappingField\MappingFieldInterface;
use Wowstack\Dbc\MappingField\MappingException;

/**
 * Implements the column to field type maps for reading DBC files.
 */
class Mapping
{
    /**
     * @var MappingFieldInterface[]
     */
    protected $fields = [];

    /**
     * @var int
     */
    protected $fieldCount = 0;

    /**
     * @var int
     */
    protected $fieldSize = 0;

    /**
     * @var bool
     */
    protected $hasStrings = false;

    /**
     * Create an instance.
     *
     * @param array $mapping
     *
     * @throws MappingException
     */
    public function __construct(array $mapping = [])
    {
        $fields = isset($mapping['fields']) ? $mapping['fields'] : [];

        foreach ($fields as $fieldName => $fieldData) {
            $this->add($fieldName, $fieldData);
            $this->fieldCount += $this->fields[$fieldName]->getTotalCount();
            $this->fieldSize += $this->fields[$fieldName]->getTotalSize();

            if ('string' === $this->fields[$fieldName]->getType() ||
            'localized_string' === $this->fields[$fieldName]->getType()) {
                $this->hasStrings = true;
            }
        }
    }

    /**
     * Adds a field type to the mapping list.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @throws MappingException
     */
    public function add(string $name, array $parameters)
    {
        if (!isset($parameters['type'])) {
            throw new MappingException('Field definition is missing a type.');
        }

        switch ($parameters['type']) {
            case 'float':
                $field = new Mappings\FloatField($name, $parameters);
                break;
            case 'localized_string':
                $field = new Mappings\LocalizedStringField($name, $parameters);
                break;
            case 'char':
                $field = new Mappings\SignedCharField($name, $parameters);
                break;
            case 'int':
                $field = new Mappings\SignedIntegerField($name, $parameters);
                break;
            case 'string':
                $field = new Mappings\StringField($name, $parameters);
                break;
            case 'uchar':
                $field = new Mappings\UnsignedCharField($name, $parameters);
                break;
            case 'uint':
                $field = new Mappings\UnsignedIntegerField($name, $parameters);
                break;
            case 'foreign_key':
                $field = new Mappings\ForeignKeyField($name, $parameters);
                break;
            default:
                throw new MappingException('Unknown field type specified');
        }

        $this->fields[$name] = $field;
    }

    /**
     * Returns the amount of fields in the mapping.
     *
     * @return int
     */
    public function getFieldCount(): int
    {
        return $this->fieldCount;
    }

    /**
     * Returns the actual amount of columns in the mapping.
     *
     * @return int
     */
    public function getFieldSize(): int
    {
        return $this->fieldSize;
    }

    /**
     * Returns the mapping type for a field.
     *
     * @param string $name
     *
     * @return string
     */
    public function getFieldType(string $name): string
    {
        return $this->fields[$name]->getType();
    }

    /**
     * Create an instance with a mapping from file.
     *
     * @param string $yaml path to YAML file
     *
     * @return Mapping
     *
     * @throws MappingException
     */
    public static function fromYAML(string $yaml): Mapping
    {
        return new self(Yaml::parseFile($yaml));
    }

    /**
     * Returns if a string field is part of the mapping.
     *
     * @return bool
     */
    public function hasStrings(): bool
    {
        return $this->hasStrings;
    }

    /**
     * Returns a list of field names.
     *
     * @return array
     */
    public function getFieldNames(): array
    {
        $fieldNames = [];

        foreach ($this->fields as $field) {
            $fieldList = $field->getParsedFields();
            foreach ($fieldList as $fieldName => $fieldData) {
                $fieldNames[] = $fieldName;
            }
        }

        return $fieldNames;
    }

    /**
     * Returns the resulting parsed field data.
     *
     * @var array
     *
     * @return array
     */
    public function getParsedFields(): array
    {
        $parsedFields = [];

        foreach ($this->fields as $field) {
            $fieldList = $field->getParsedFields();
            foreach ($fieldList as $fieldName => $fieldData) {
                $parsedFields[$fieldName] = $fieldData;
            }
        }

        return $parsedFields;
    }
}
