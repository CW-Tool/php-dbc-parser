<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\Mapping;

class DBCTest extends TestCase
{
    /**
     * Checks that DBC files can be loaded with a mapping.
     *
     * @dataProvider constructProvider
     */
    public function testItConstructs(string $yaml, string $dbc, int $record_count, int $record_size, int $field_count, int $string_block_size, bool $has_strings, int $string_count)
    {
        $DBC = new DBC($dbc, Mapping::fromYAML($yaml));
        $this->assertInstanceOf(DBC::class, $DBC);

        $this->assertEquals($dbc, $DBC->getPath());
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
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/AreaPOI.yaml', dirname(__FILE__).'/data/AreaPOI.dbc', 339, 116, 29, 3856, true, 254],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/Spell.yaml', dirname(__FILE__).'/data/Spell.dbc', 22357, 692, 173, 833949, true, 19140],
        ];
    }
}
