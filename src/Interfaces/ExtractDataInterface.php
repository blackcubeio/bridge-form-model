<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeFormModel\Interfaces;

/**
 * Form model able to return its data as an array, reloadable with load($data, '').
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
interface ExtractDataInterface
{
    /**
     * Returns the values of the active scenario fields, keyed by field name.
     *
     * @return array<string, mixed>
     */
    public function extractData(): array;
}
