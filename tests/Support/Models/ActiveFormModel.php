<?php

declare(strict_types=1);

/**
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */

namespace Blackcube\BridgeFormModel\Tests\Support\Models;

use Blackcube\BridgeFormModel\Attributes\Bridge;
use Blackcube\BridgeFormModel\BridgeFormModel;

/**
 * FormModel with isActive() and setActive() separately bridged.
 * This is the case that was broken: second setEndpoint() was overwriting first.
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ActiveFormModel extends BridgeFormModel
{
    private bool $active = false;

    #[Bridge]
    public function isActive(): bool
    {
        return $this->active;
    }

    #[Bridge]
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
