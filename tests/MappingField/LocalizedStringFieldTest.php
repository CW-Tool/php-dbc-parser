<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\LocalizedStringField;
use Wowstack\Dbc\MappingField\MappingException;

class LocalizedStringFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
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
     */
    public function testItFailsWithoutParams(string $name, array $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new LocalizedStringField($name, $parameters);
    }

    /**
     * @dataProvider optionalParamProvider
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
     */
    public function testItCalculatesTheCorectSize(string $name, array $parameters, int $size)
    {
        $field = new LocalizedStringField($name, $parameters);

        $this->assertEquals($size, $field->getTotalSize());
    }

    /**
     * @dataProvider parsedFieldProvider
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
        ];
    }
}
