<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeFormModel\Traits;

/**
 * Implements ExtractDataInterface for a BridgeFormModel.
 * Applied explicitly on the form models that must return their data as an array.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
trait ExtractDataTrait
{
    /**
     * Returns the values of the active scenario fields, keyed by field name.
     *
     * @return array<string, mixed>
     */
    public function extractData(): array
    {
        $data = [];
        foreach ($this->getActiveFields() as $field) {
            $data[$field] = $this->getPropertyValue($field);
        }

        return $data;
    }
}
