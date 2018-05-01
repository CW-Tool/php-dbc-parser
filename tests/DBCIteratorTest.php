<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\DBCIterator;
use Wowstack\Dbc\Mapping;

class DBCIteratorTest extends TestCase
{
    /**
     * Checks that DBC provide a valid iterator.
     *
     * @dataProvider constructProvider
     */
    public function testItConstructs(string $yaml, string $dbc)
    {
        $DBC = new DBC($dbc, Mapping::fromYAML($yaml));
        $DBCIterator = $DBC->getIterator();
        $this->assertInstanceOf(DBCIterator::class, $DBCIterator);
    }

    /**
     * @return array
     */
    public function constructProvider()
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/AreaPOI.yaml', dirname(__FILE__).'/data/AreaPOI.dbc'],
        ];
    }
}
