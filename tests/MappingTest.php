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
    public function testItConstructs($sample_yaml, $field_count, $field_size)
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
    public function testItFailsWithUnknownTypes($sample_yaml)
    {
        $this->expectException(MappingException::class);
        $mapping = Mapping::fromYAML($sample_yaml);
    }

    /**
     * Tests that mapping fails to load with invalid types.
     *
     * @dataProvider missingTypeProvider
     */
    public function testItFailsWithMissingTypes($sample_yaml)
    {
        $this->expectException(MappingException::class);
        $mapping = Mapping::fromYAML($sample_yaml);
    }

    /**
     * Tests that mapping loads with various definitions.
     *
     * @dataProvider typeTestProvider
     */
    public function testItProvidesTypeInformation($sample_yaml, $field_name, $field_type)
    {
        $mapping = Mapping::fromYAML($sample_yaml);
        $this->assertEquals($field_type, $mapping->getFieldType($field_name));
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function sampleProvider()
    {
        return [
            'valid example mapping' => [dirname(__FILE__).'/data/sample.yaml', 15, 54],
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/AreaPOI.yaml', 29, 116],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/Spell.yaml', 173, 692],
        ];
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function invalidSampleProvider()
    {
        return [
            'example with unknown types' => [dirname(__FILE__).'/data/invalid-sample.yaml'],
        ];
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function missingTypeProvider()
    {
        return [
            'example without types' => [dirname(__FILE__).'/data/missing-type.yaml'],
        ];
    }

    /**
     * Provides a list of YAML sample files.
     *
     * @return array
     */
    public function typeTestProvider()
    {
        return [
            'example with string column' => [dirname(__FILE__).'/data/type-sample.yaml', 'name', 'string'],
        ];
    }
}
