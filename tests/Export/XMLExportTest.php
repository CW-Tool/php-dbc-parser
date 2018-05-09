<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests\Export;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\Mapping;
use Wowstack\Dbc\Export\XMLExport;
use Wowstack\Dbc\DBCException;

/**
 * Verifies DBC files can be exported to XML files.
 */
class XMLExportTest extends TestCase
{
    /**
     * @dataProvider constructProvider
     *
     * @param string $yaml
     * @param string $dbc
     */
    public function testItConstructs(string $yaml, string $dbc)
    {
        $mapping = Mapping::fromYAML($yaml);
        $DBC = new DBC($dbc, $mapping);
        $XMLExport = new XMLExport();
        $this->assertInstanceOf(XMLExport::class, $XMLExport);
    }

    /**
     * @dataProvider noMappingProvider
     *
     * @param string $dbc
     * @param string $export_path
     */
    public function testItFailsWithoutMapping(string $dbc, string $export_path)
    {
        $this->expectException(DBCException::class);
        $DBC = new DBC($dbc);
        $XMLExport = new XMLExport();
        $XMLExport->export($DBC, $export_path);
    }

    /**
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/../data/maps/AreaPOI.yaml', dirname(__FILE__).'/../data/AreaPOI.dbc'],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/../data/maps/BankBagSlotPrices.yaml', dirname(__FILE__).'/../data/BankBagSlotPrices.dbc'],
        ];
    }

    /**
     * @return array
     */
    public function noMappingProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/../data/AreaPOI.dbc', dirname(__FILE__).'/../data/AreaPOI.xml'],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/../data/BankBagSlotPrices.dbc', dirname(__FILE__).'/../data/BankBagSlotPrices.xml'],
        ];
    }
}
