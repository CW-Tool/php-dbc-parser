<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\Mapping;
use Wowstack\Dbc\MappingField\MappingException;

class MappingTest extends TestCase
{
    /**
     * Tests that mapping loads with various definitions.
     *
     * @dataProvider sampleProvider
     */
    public function testItConstructs(string $sample_yaml, int $field_count, int $field_size)
    {
        $mapping = Mapping::fromYAML($sample_yaml);
        $this->assertInstanceOf(Mapping::class, $mapping);
        $this->assertEquals($field_count, $mapping->getFieldCount());
        $this->assertEquals($field_size, $mapping->getFieldSize());
    }

    /**
     * Tests that mapping fails to load with invalid types.
     *
     * @dataProvider invalidSampleProvider
     */
    public function testItFailsWithUnknownTypes(string $sample_yaml)
    {
        $this->expectException(MappingException::class);
        $mapping = Mapping::fromYAML($sample_yaml);
    }

    /**
     * Tests that mapping fails to load with invalid types.
     *
     * @dataProvider missingTypeProvider
     */
    public function testItFailsWithMissingTypes(string $sample_yaml)
    {
        $this->expectException(MappingException::class);
        $mapping = Mapping::fromYAML($sample_yaml);
    }

    /**
     * Tests that mapping loads with various definitions.
     *
     * @dataProvider typeTestProvider
     */
    public function testItProvidesTypeInformation(string $sample_yaml, string $field_name, string $field_type)
    {
        $mapping = Mapping::fromYAML($sample_yaml);
        $this->assertEquals($field_type, $mapping->getFieldType($field_name));
    }

    /**
     * Tests that the mapping producs a valid parsed field list for reading data.
     *
     * @dataProvider parsedFieldProvider
     */
    public function testItCreatesValidParsedFields(string $sample_yaml, array $parsed_fields)
    {
        $mapping = Mapping::fromYAML($sample_yaml);
        $this->assertEquals($parsed_fields, $mapping->getParsedFields());
    }

    /**
     * Tests that the mapping producs a valid parsed field list for reading data.
     *
     * @dataProvider fieldNamesProvider
     */
    public function testItProvidesFieldNames(string $sample_yaml, array $field_names)
    {
        $mapping = Mapping::fromYAML($sample_yaml);
        $this->assertEquals($field_names, $mapping->getFieldNames());
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function sampleProvider(): array
    {
        return [
            'valid example mapping' => [dirname(__FILE__).'/data/maps/sample.yaml', 24, 90],
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/AreaPOI.yaml', 29, 116],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/BankBagSlotPrices.yaml', 2, 8],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/Spell.yaml', 173, 692],
        ];
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function invalidSampleProvider(): array
    {
        return [
            'example with unknown types' => [dirname(__FILE__).'/data/maps/invalid-sample.yaml'],
        ];
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function missingTypeProvider(): array
    {
        return [
            'example without types' => [dirname(__FILE__).'/data/maps/missing-type.yaml'],
        ];
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function typeTestProvider(): array
    {
        return [
            'example with string column' => [dirname(__FILE__).'/data/maps/type-sample.yaml', 'name', 'string'],
        ];
    }

    /**
     * Returns a list of fields and the expected parsing result.
     *
     * @return array
     */
    public function parsedFieldProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [
                dirname(__FILE__).'/data/maps/AreaPOI.yaml',
                [
                    'id' => [
                        'type' => 'uint',
                        'size' => 4,
                        'format' => 'V1id',
                        'offset' => 0,
                    ],
                    'importance' => [
                        'type' => 'int',
                        'size' => 4,
                        'format' => 'l1importance',
                        'offset' => 0,
                    ],
                    'icon' => [
                        'type' => 'int',
                        'size' => 4,
                        'format' => 'l1icon',
                        'offset' => 0,
                    ],
                    'factionID' => [
                        'type' => 'uint',
                        'size' => 4,
                        'format' => 'V1factionID',
                        'offset' => 0,
                    ],
                    'locationX' => [
                        'type' => 'float',
                        'size' => 4,
                        'format' => 'g1locationX',
                        'offset' => 0,
                    ],
                    'locationY' => [
                        'type' => 'float',
                        'size' => 4,
                        'format' => 'g1locationY',
                        'offset' => 0,
                    ],
                    'locationZ' => [
                        'type' => 'float',
                        'size' => 4,
                        'format' => 'g1locationZ',
                        'offset' => 0,
                    ],
                    'mapID' => [
                        'type' => 'uint',
                        'size' => 4,
                        'format' => 'V1mapID',
                        'offset' => 0,
                    ],
                    'flags' => [
                        'type' => 'int',
                        'size' => 4,
                        'format' => 'l1flags',
                        'offset' => 0,
                    ],
                    'areaTableID' => [
                        'type' => 'int',
                        'size' => 4,
                        'format' => 'l1areaTableID',
                        'offset' => 0,
                    ],
                    'name' => [
                        'type' => 'localized_string',
                        'size' => 36,
                        'format' => 'V1name/V1name_unused1/V1name_unused2/V1name_unused3/V1name_unused4/V1name_unused5/V1name_unused6/V1name_unused7/V1name_checksum',
                        'offset' => 0,
                    ],
                    'description' => [
                        'type' => 'localized_string',
                        'size' => 36,
                        'format' => 'V1description/V1description_unused1/V1description_unused2/V1description_unused3/V1description_unused4/V1description_unused5/V1description_unused6/V1description_unused7/V1description_checksum',
                        'offset' => 0,
                    ],
                    'worldStateID' => [
                        'type' => 'uint',
                        'size' => 4,
                        'format' => 'V1worldStateID',
                        'offset' => 0,
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the list of expected field names from a mapping.
     *
     * @return array
     */
    public function fieldNamesProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [
                dirname(__FILE__).'/data/maps/AreaPOI.yaml',
                [
                    'id',
                    'importance',
                    'icon',
                    'factionID',
                    'locationX',
                    'locationY',
                    'locationZ',
                    'mapID',
                    'flags',
                    'areaTableID',
                    'name',
                    'description',
                    'worldStateID',
                ],
            ],
        ];
    }
}
