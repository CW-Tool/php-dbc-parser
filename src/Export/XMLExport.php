<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Export;

use Wowstack\Dbc\DBC;
use Wowstack\Dbc\DBCException;

class XMLExport implements ExportInterface
{
    /**
     * {@inheritdoc}
     */
    public function export(DBC $dbc, string $target_path = 'php://output', string $version = '1.12.1')
    {
        if (null === $dbc->getMap()) {
            throw new DBCException('DBC export requires a map.');
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $edbc = $dom->appendChild($dom->createElement('dbc'));
        $edbc_name = $dom->createAttribute('name');
        $edbc_name->value = $dbc->getName();
        $edbc->appendChild($edbc_name);

        $edbc_version = $dom->createAttribute('version');
        $edbc_version->value = $version;
        $edbc->appendChild($edbc_version);

        $efields = $edbc->appendChild($dom->createElement('fields'));
        $erecords = $edbc->appendChild($dom->createElement('records'));

        $fields = $dbc->getMap()->getParsedFields();
        foreach ($fields as $field_name => $field_data) {
            $efields->appendChild($dom->createElement($field_name, $field_data['type']));
        }

        foreach ($dbc as $index => $record) {
            $pairs = $record->read();
            $erecord = $erecords->appendChild($dom->createElement('record'));
            foreach ($pairs as $field => $value) {
                $attr = $dom->createAttribute($field);
                if (is_string($value)) {
                    $attr->value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
                } else {
                    $attr->value = $value;
                }
                $erecord->appendChild($attr);
            }
        }

        $data = $dom->saveXML();
        file_put_contents($target_path, $data);
    }
}