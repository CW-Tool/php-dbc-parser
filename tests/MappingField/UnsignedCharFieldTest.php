<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\UnsignedCharField;
use Wowstack\Dbc\MappingField\MappingException;

class UnsignedCharFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     */
    public function testItConstructs(string $name, array $parameters, int $size)
    {
        $field = new UnsignedCharField($name, $parameters);

        $this->assertInstanceOf(UnsignedCharField::class, $field);
        $this->assertEquals('uchar', $field->getType());
        $this->assertEquals($name, $field->getName());
        $this->assertEquals($parameters['count'], $field->getCount());
        $this->assertEquals($size, $field->getSize());
    }

    /**
     * @dataProvider incompleteProvider
     */
    public function testItFailsWithoutParams(string $name, array $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new UnsignedCharField($name, $parameters);
    }

    /**
     * @dataProvider parsedFieldProvider
     */
    public function testItCreatesParsedFields(string $name, array $parameters, array $parsed_fields)
    {
        $field = new UnsignedCharField($name, $parameters);
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
            'single column' => ['name', ['type' => 'uchar', 'count' => 1], 1],
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
            'missing count' => ['name', ['type' => 'uchar']],
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
                'name', ['type' => 'uchar', 'count' => 1],
                [
                    'name' => [
                        'type' => 'uchar',
                        'size' => 1,
                        'format' => 'C1name',
                        'offset' => 0,
                    ],
                ],
            ],
            'multiple columns' => [
                'name', ['type' => 'uchar', 'count' => 2],
                [
                    'name1' => [
                        'type' => 'uchar',
                        'size' => 1,
                        'format' => 'C1name1',
                        'offset' => 0,
                    ],
                    'name2' => [
                        'type' => 'uchar',
                        'size' => 1,
                        'format' => 'C1name2',
                        'offset' => 0,
                    ],
                ],
            ],
        ];
    }
}
