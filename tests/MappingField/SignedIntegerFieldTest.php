<?php
declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\SignedIntegerField;
use Wowstack\Dbc\MappingField\MappingException;

class SignedIntegerFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     */
    public function testItConstructs($name, $parameters, $size)
    {
        $field = new SignedIntegerField($name, $parameters);

        $this->assertInstanceOf(SignedIntegerField::class, $field);
        $this->assertEquals('int', $field->getType());
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
        $field = new SignedIntegerField($name, $parameters);
    }

    /**
     * Provides a set of sample data to construct a field.
     *
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'single column' => ['name', ['type' => 'int', 'count' => 1], 4],
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
            'missing count' => ['name', ['type' => 'int']],
        ];
    }
}
