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
    public function testItConstructs($name, $parameters, $count, $size)
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
    public function testItFailsWithoutParams($name, $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new LocalizedStringField($name, $parameters);
    }

    /**
     * @dataProvider optionalParamProvider
     */
    public function testItAcceptsOptionalParams($name, $parameters)
    {
        $field = new LocalizedStringField($name, $parameters);

        $this->assertInstanceOf(LocalizedStringField::class, $field);
        $this->assertEquals($parameters['locale'], $field->getLocale());
        $this->assertEquals($parameters['locale_count'], $field->getLocaleCount());
    }

    /**
     * @dataProvider sizeCalculateProvider
     */
    public function testItCalculatesTheCorectSize($name, $parameters, $size)
    {
        $field = new LocalizedStringField($name, $parameters);

        $this->assertEquals($size, $field->getTotalSize());
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'single column' => ['name', ['type' => 'localized_string', 'count' => 1], 9, 36],
            'single column with extended locales' => ['name2', ['type' => 'localized_string', 'count' => 1, 'locale' => 'enGB', 'locale_count' => 16], 17, 68],
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
}
