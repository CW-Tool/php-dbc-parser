<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\SignedCharField;
use Wowstack\Dbc\MappingField\MappingException;

/**
 * Verifies signed char fields can be used in all variations.
 */
class SignedCharFieldTest extends TestCase
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
        $field = new SignedCharField($name, $parameters);

        $this->assertInstanceOf(SignedCharField::class, $field);
        $this->assertEquals('char', $field->getType());
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
        $field = new SignedCharField($name, $parameters);
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
        $field = new SignedCharField($name, $parameters);
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
            'single column' => ['name', ['type' => 'char', 'count' => 1], 1],
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
            'missing count' => ['name', ['type' => 'char']],
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
                'name', ['type' => 'char', 'count' => 1],
                [
                    'name' => [
                        'type' => 'char',
                        'size' => 1,
                        'format' => 'c1name',
                        'offset' => 0,
                    ],
                ],
            ],
            'multiple columns' => [
                'name', ['type' => 'char', 'count' => 2],
                [
                    'name1' => [
                        'type' => 'char',
                        'size' => 1,
                        'format' => 'c1name1',
                        'offset' => 0,
                    ],
                    'name2' => [
                        'type' => 'char',
                        'size' => 1,
                        'format' => 'c1name2',
                        'offset' => 0,
                    ],
                ],
            ],
        ];
    }
}
