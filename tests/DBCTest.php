<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Tests;

use PHPUnit\Framework\TestCase;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\DBCException;
use Wowstack\Dbc\Mapping;

class DBCTest extends TestCase
{
    /**
     * Checks that DBC files can be loaded with a mapping.
     *
     * @dataProvider constructProvider
     */
    public function testItConstructs(string $yaml, string $dbc,
        int $record_count, int $record_size, int $field_count, int $string_block_size,
        bool $has_strings, int $string_count)
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
        $this->assertInstanceOf(Mapping::class, $DBC->getMap());
    }

    /**
     * Checks that DBC throws exceptions for invalid records.
     *
     * @dataProvider exceptionProvider
     */
    public function testItThrowsExceptionsForUnknownRecords(string $yaml, string $dbc)
    {
        $this->expectException(DBCException::class);

        $DBC = new DBC($dbc, Mapping::fromYAML($yaml));
        $DBC->getRecord($DBC->getRecordCount() + 1);
    }

    /**
     * @dataProvider nameProvider
     */
    public function testItHasAValidName(string $dbc, string $name)
    {
        $DBC = new DBC($dbc);
        $this->assertEquals($name, $DBC->getName());
    }

    /**
     * @dataProvider fileNotFoundProvider
     */
    public function testItFailsWithMissingFile(string $dbc)
    {
        $this->expectException(DBCException::class);
        $DBC = new DBC($dbc);
    }

    /**
     * @dataProvider tooSmallProvider
     */
    public function testItFailsWithIncompleteHeader(string $dbc)
    {
        $this->expectException(DBCException::class);
        $DBC = new DBC($dbc);
    }

    /**
     * @dataProvider invalidHeaderProvider
     */
    public function testItFailsWithInvalidHeader(string $dbc)
    {
        $this->expectException(DBCException::class);
        $DBC = new DBC($dbc);
    }

    /**
     * @return array
     */
    public function constructProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/AreaPOI.yaml', dirname(__FILE__).'/data/AreaPOI.dbc', 339, 116, 29, 3856, true, 254],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/BankBagSlotPrices.yaml', dirname(__FILE__).'/data/BankBagSlotPrices.dbc', 12, 8, 2, 1, false, 0],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/Spell.yaml', dirname(__FILE__).'/data/Spell.dbc', 22357, 692, 173, 833949, true, 19140],
        ];
    }

    /**
     * @return array
     */
    public function exceptionProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/AreaPOI.yaml', dirname(__FILE__).'/data/AreaPOI.dbc'],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/BankBagSlotPrices.yaml', dirname(__FILE__).'/data/BankBagSlotPrices.dbc'],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/maps/Spell.yaml', dirname(__FILE__).'/data/Spell.dbc'],
        ];
    }

    /**
     * @return array
     */
    public function nameProvider(): array
    {
        return [
            'AreaPOI mapping - patch 1.12.1' => [dirname(__FILE__).'/data/AreaPOI.dbc', 'AreaPOI'],
            'BankBagSlotPrices mapping - patch 1.12.1' => [dirname(__FILE__).'/data/BankBagSlotPrices.dbc', 'BankBagSlotPrices'],
            'Spell mapping - patch 1.12.1' => [dirname(__FILE__).'/data/Spell.dbc', 'Spell'],
        ];
    }

    /**
     * @return array
     */
    public function fileNotFoundProvider(): array
    {
        return [
            'secret bank account storage access codes' => [dirname(__FILE__).'/data/access-codes.dbc'],
            'lottery winners list 2032' => [dirname(__FILE__).'/data/lottery-winners.dbc'],
        ];
    }

    /**
     * @return array
     */
    public function tooSmallProvider(): array
    {
        return [
            'catch me if you can' => [dirname(__FILE__).'/data/header-only.dbc'],
        ];
    }

    /**
     * @return array
     */
    public function invalidHeaderProvider(): array
    {
        return [
            'binary star system' => [dirname(__FILE__).'/data/size-matters.dbc'],
            'binary star system' => [dirname(__FILE__).'/data/wrong-signature.dbc'],
        ];
    }
}
