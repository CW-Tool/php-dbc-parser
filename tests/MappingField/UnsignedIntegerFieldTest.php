<?php
declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\UnsignedIntegerField;
use Wowstack\Dbc\MappingField\MappingException;

class UnsignedIntegerFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     */
    public function testItConstructs($name, $parameters, $size)
    {
        $field = new UnsignedIntegerField($name, $parameters);

        $this->assertInstanceOf(UnsignedIntegerField::class, $field);
        $this->assertEquals('uint', $field->getType());
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
        $field = new UnsignedIntegerField($name, $parameters);
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'without options' => ['name', ['type' => 'uint', 'count' => 1], 4],
        ];
    }

    /**
     * Provides a set of incomplete sample data to construct a field.
     *
     * @return array
     */
    public function incompleteProvider(): array
    {
        return [
            'without options' => ['name', ['type' => 'uint']],
        ];
    }
}
