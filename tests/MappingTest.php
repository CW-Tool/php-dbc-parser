<?php
declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\Mapping;

class MappingTest extends TestCase
{
    /**
     * @var string
     */
    protected $yaml_mapping;

    public function setUp()
    {
        $this->yaml_mapping = realpath(dirname(__FILE__).'/data/sample.yaml');
    }

    public function testItConstructsFromMap()
    {
        $this->assertInstanceOf(Mapping::class, Mapping::fromYAML($this->yaml_mapping));
    }
}
