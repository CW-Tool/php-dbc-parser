<?php
declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\StringField;
use Wowstack\Dbc\MappingField\MappingException;

class StringFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     */
    public function testItConstructs($name, $parameters, $size)
    {
        $field = new StringField($name, $parameters);

        $this->assertInstanceOf(StringField::class, $field);
        $this->assertEquals('string', $field->getType());
        $this->assertEquals($name, $field->getName());
        $this->assertEquals($parameters['count'], $field->getCount());
        $this->assertEquals($size, $field->getSize());
    }

    /**
     * @dataProvider incompleteProvider
     */
    public function testItFailsWithoutParams($name, $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new StringField($name, $parameters);
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'without options' => ['name', ['type' => 'string', 'count' => 1], 4],
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
            'without options' => ['name', ['type' => 'string']],
        ];
    }
}
