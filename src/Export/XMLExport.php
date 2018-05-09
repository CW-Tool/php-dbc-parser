<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Export;

use Doctrine\Common\Inflector\Inflector;
use Wowstack\Dbc\DBC;
use Wowstack\Dbc\DBCException;

/**
 * Implements conversion of DBC files and their records into XML files.
 */
class XMLExport implements ExportInterface
{
    /**
     * {@inheritdoc}
     */
    public function export(DBC $dbc, string $targetPath = 'php://output', string $version = '1.12.1')
    {
        if (null === $dbc->getMap()) {
            throw new DBCException('DBC export requires a map.');
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = true;

        $dbcRoot = $dom->appendChild($dom->createElement('dbc'));

        $dbcRootName = $dom->createAttribute('name');
        $dbcRootName->value = Inflector::pluralize($dbc->getName());
        $dbcRoot->appendChild($dbcRootName);

        $dbcRootVersion = $dom->createAttribute('version');
        $dbcRootVersion->value = $version;
        $dbcRoot->appendChild($dbcRootVersion);

        $erecords = $dbcRoot->appendChild($dom->createElement(Inflector::pluralize($dbc->getName())));

        foreach ($dbc as $index => $record) {
            $pairs = $record->read();
            $erecord = $erecords->appendChild($dom->createElement(Inflector::singularize($dbc->getName())));
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
        file_put_contents($targetPath, $data);
    }
}
