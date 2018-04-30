<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\Mapping;

class DBCTest extends TestCase
{
    /**
     * @var string
     */
    protected $sample_yaml = '';

    /**
     * @var Mapping
     */
    protected $sample_mapping = null;

    /**
     * @var string
     */
    protected $sample_dbc = '';

    /**
     * Prepares sample DBC and mapping.
     */
    public function setUp()
    {
        $this->sample_yaml = dirname(__FILE__).'/data/AreaPOI.yaml';
        $this->sample_dbc = dirname(__FILE__).'/data/AreaPOI.dbc';

        $this->sample_mapping = Mapping::fromYAML($this->sample_yaml);
    }

    /**
     * Checks that DBC files can be loaded with a mapping.
     *
     * @dataProvider constructProvider
     */
    public function testItConstructs($record_count, $record_size, $field_count, $string_block_size, $has_strings, $string_count)
    {
        $DBC = new DBC($this->sample_dbc, $this->sample_mapping);
        $this->assertInstanceOf(DBC::class, $DBC);

        $this->assertEquals($this->sample_dbc, $DBC->getPath());

        $this->assertEquals($record_count, $DBC->getRecordCount());
        $this->assertEquals($record_size, $DBC->getRecordSize());
        $this->assertEquals($field_count, $DBC->getFieldCount());
        $this->assertEquals($string_block_size, $DBC->getStringBlockSize());
        $this->assertEquals($has_strings, $DBC->hasStrings());
        $this->assertCount($string_count, $DBC->getStringBlock());
    }

    /**
     * @return array
     */
    public function constructProvider()
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [339, 116, 29, 3856, true, 254],
        ];
    }
}
