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
 * FormModel with explicit getter/setter in Bridge attribute
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class ExplicitGetterSetterFormModel extends BridgeFormModel
{
    #[Bridge(getter: 'getEmail', setter: 'setEmail')]
    public string $contactEmail = '';

    public function rules(): array
    {
        return [];
    }

    public function getRules(): array
    {
        return $this->rules();
    }
}
