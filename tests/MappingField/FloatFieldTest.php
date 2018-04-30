<?php
declare(strict_types=1);

namespace Wowstack\Dbc\Tests\MappingField;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\MappingField\FloatField;
use Wowstack\Dbc\MappingField\MappingException;

class FloatFieldTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     */
    public function testItConstructs($name, $parameters, $size)
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
     */
    public function testItFailsWithoutParams($name, $parameters)
    {
        $this->expectException(MappingException::class);
        $field = new FloatField($name, $parameters);
    }

    /**
     * @dataProvider sizeCalculateProvider
     */
    public function testItCalculatesTheCorectSize($name, $parameters, $size)
    {
        $field = new FloatField($name, $parameters);

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
            'single column' => ['name', ['type' =>'float', 'count' => 1], 4],
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
            'missing count' => ['name', ['type' =>'float'], 4],
        ];
    }

    /**
     * Provides a sample set and the expected total size
     *
     * @return array
     */
    public function sizeCalculateProvider(): array
    {
        return [
            'single column' => ['name', ['type' =>'float', 'count' => 1], 4],
            'multiple columns' => ['name', ['type' =>'float', 'count' => 4], 16],
        ];
    }
}
