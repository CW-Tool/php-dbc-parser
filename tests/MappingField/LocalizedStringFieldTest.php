<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\LocalizedStringField;
use Wowstack\Dbc\MappingField\MappingException;

/**
 * Verifies localized string fields can be used in all variations.
 */
class LocalizedStringFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     *
     * @param string $name
     * @param array  $parameters
     * @param int    $count
     * @param int    $size
     */
    public function testItConstructs(string $name, array $parameters, int $count, int $size)
    {
        $field = new LocalizedStringField($name, $parameters);

        $this->assertInstanceOf(LocalizedStringField::class, $field);
        $this->assertEquals('localized_string', $field->getType());
        $this->assertEquals($name, $field->getName());
        $this->assertEquals($count, $field->getCount());
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
        $field = new LocalizedStringField($name, $parameters);
    }

    /**
     * @dataProvider optionalParamProvider
     *
     * @param string $name
     * @param array  $parameters
     */
    public function testItAcceptsOptionalParams(string $name, array $parameters)
    {
        $field = new LocalizedStringField($name, $parameters);

        $this->assertInstanceOf(LocalizedStringField::class, $field);
        $this->assertEquals($parameters['locale'], $field->getLocale());
        $this->assertEquals($parameters['locale_count'], $field->getLocaleCount());
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
        $field = new LocalizedStringField($name, $parameters);

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
        $field = new LocalizedStringField($name, $parameters);
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
            'single column' => ['name', ['type' => 'localized_string', 'count' => 1], 1, 36],
            'single column with extended locales' => ['name2', ['type' => 'localized_string', 'count' => 1, 'locale' => 'enGB', 'locale_count' => 16], 1, 68],
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
            'missing count' => ['name', ['type' => 'localized_string']],
            'missing count with extended locales' => ['name2', ['type' => 'localized_string', 'locale' => 'enGB', 'locale_count' => 16]],
        ];
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function optionalParamProvider(): array
    {
        return [
            'using 8 locales' => ['name', ['type' => 'localized_string', 'count' => 1, 'locale' => 'enGB', 'locale_count' => 8]],
            'using 16 locales' => ['name', ['type' => 'localized_string', 'count' => 1, 'locale' => 'deDE', 'locale_count' => 16]],
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
            'using 8 locales' => ['name', ['type' => 'localized_string', 'count' => 1, 'locale' => 'enGB', 'locale_count' => 8], 36],
            'using 16 locales' => ['name', ['type' => 'localized_string', 'count' => 1, 'locale' => 'deDE', 'locale_count' => 16], 68],
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
            'single column, enUS locale' => [
                'name', ['type' => 'localized_string', 'count' => 1],
                [
                    'name' => [
                        'type' => 'localized_string',
                        'size' => 36,
                        'format' => 'V1name/V1name_unused1/V1name_unused2/V1name_unused3/V1name_unused4/V1name_unused5/V1name_unused6/V1name_unused7/V1name_checksum',
                        'offset' => 0,
                    ],
                ],
            ],
            'single column, deDE locale' => [
                'name', ['type' => 'localized_string', 'count' => 1, 'locale' => 'deDE'],
                [
                    'name' => [
                        'type' => 'localized_string',
                        'size' => 36,
                        'format' => 'V1name_unused0/V1name_unused1/V1name_unused2/V1name/V1name_unused4/V1name_unused5/V1name_unused6/V1name_unused7/V1name_checksum',
                        'offset' => 3,
                    ],
                ],
            ],
            'multiple columns, enUS locale' => [
                'name', ['type' => 'localized_string', 'count' => 2],
                [
                    'name1' => [
                        'type' => 'localized_string',
                        'size' => 36,
                        'format' => 'V1name1/V1name1_unused1/V1name1_unused2/V1name1_unused3/V1name1_unused4/V1name1_unused5/V1name1_unused6/V1name1_unused7/V1name1_checksum',
                        'offset' => 0,
                    ],
                    'name2' => [
                        'type' => 'localized_string',
                        'size' => 36,
                        'format' => 'V1name2/V1name2_unused1/V1name2_unused2/V1name2_unused3/V1name2_unused4/V1name2_unused5/V1name2_unused6/V1name2_unused7/V1name2_checksum',
                        'offset' => 0,
                    ],
                ],
            ],
        ];
    }
}
