<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\DBCRecord;
use Wowstack\Dbc\Mapping;

class DBCRecordTest extends TestCase
{
    /**
     * Checks that DBC provide a valid record.
     *
     * @dataProvider constructProvider
     */
    public function testItConstructs(string $yaml, string $dbc, int $record)
    {
        $DBC = new DBC($dbc, Mapping::fromYAML($yaml));
        $DBCRecord = $DBC->getRecord($record);
        $this->assertInstanceOf(DBCRecord::class, $DBCRecord);
        $data = $DBCRecord->read();
    }

    /**
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/AreaPOI.yaml', dirname(__FILE__).'/data/AreaPOI.dbc', 0],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/BankBagSlotPrices.yaml', dirname(__FILE__).'/data/BankBagSlotPrices.dbc', 0],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/Spell.yaml', dirname(__FILE__).'/data/Spell.dbc', 0],
        ];
    }
}
