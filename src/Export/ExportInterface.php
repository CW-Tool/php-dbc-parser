<?php

declare(strict_types=1);

namespace Wowstack\Dbc\Export;

use Wowstack\Dbc\DBC;

/**
 * Defines an interface for exporters.
 */
interface ExportInterface
{
    /**
     * @param DBC    $dbc
     * @param string $targetPath
     */
    public function export(DBC $dbc, string $targetPath);
}
