<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\FloatField;
use Wowstack\Dbc\MappingField\MappingException;

/**
 * Verifies float fields can be used in all variations.
 */
class FloatFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     *
     * @param string $name
     * @param array  $parameters
     * @param int    $size
     */
    public function testItConstructs(string $name, array $parameters, int $size)
    {
        $field = new FloatField($name, $parameters);

        $this->assertInstanceOf(FloatField::class, $field);
        $this->assertEquals('float', $field->getType());
        $this->assertEquals($name, $field->getName());
        $this->assertEquals($parameters['count'], $field->getCount());
        $this->assertEquals($size, $field->getSize());
    }

    /**
     * @dataProvider incompleteProvider
     *
     * @param string $name
     * @param array  $parameters
     */
    public function testItFailsWithoutParams(string $name, array $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new FloatField($name, $parameters);
    }

    /**
     * @dataProvider sizeCalculateProvider
     *
     * @param string $name
     * @param array  $parameters
     * @param int    $size
     */
    public function testItCalculatesTheCorectSize(string $name, array $parameters, int $size)
    {
        $field = new FloatField($name, $parameters);

        $this->assertEquals($size, $field->getTotalSize());
    }

    /**
     * @dataProvider parsedFieldProvider
     *
     * @param string $name
     * @param array  $parameters
     * @param array  $parsed_fields
     */
    public function testItCreatesParsedFields(string $name, array $parameters, array $parsed_fields)
    {
        $field = new FloatField($name, $parameters);
        $this->assertEquals($parsed_fields, $field->getParsedFields());
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'single column' => ['name', ['type' => 'float', 'count' => 1], 4],
        ];
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function incompleteProvider(): array
    {
        return [
            'missing count' => ['name', ['type' => 'float'], 4],
        ];
    }

    /**
     * Provides a sample set and the expected total size.
     *
     * @return array
     */
    public function sizeCalculateProvider(): array
    {
        return [
            'single column' => ['name', ['type' => 'float', 'count' => 1], 4],
            'multiple columns' => ['name', ['type' => 'float', 'count' => 4], 16],
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
            'single column' => [
                'name', ['type' => 'float', 'count' => 1],
                [
                    'name' => [
                        'type' => 'float',
                        'size' => 4,
                        'format' => 'g1name',
                        'offset' => 0,
                    ],
                ],
            ],
            'multiple columns' => [
                'name', ['type' => 'float', 'count' => 2],
                [
                    'name1' => [
                        'type' => 'float',
                        'size' => 4,
                        'format' => 'g1name1',
                        'offset' => 0,
                    ],
                    'name2' => [
                        'type' => 'float',
                        'size' => 4,
                        'format' => 'g1name2',
                        'offset' => 0,
                    ],
                ],
            ],
        ];
    }
}
