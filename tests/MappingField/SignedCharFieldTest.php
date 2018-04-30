<?php
declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\SignedCharField;
use Wowstack\Dbc\MappingField\MappingException;

class SignedCharFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     */
    public function testItConstructs($name, $parameters, $size)
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
     */
    public function testItFailsWithoutParams($name, $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new SignedCharField($name, $parameters);
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'without options' => ['name', ['type' => 'char', 'count' => 1], 1],
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
            'without options' => ['name', ['type' => 'char']],
        ];
    }
}
