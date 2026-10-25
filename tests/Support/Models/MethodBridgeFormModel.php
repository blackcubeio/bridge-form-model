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
 * FormModel with bridged methods (getter source → setter target)
 * Note: For bidirectional transfer, we also need a setter in source
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class MethodBridgeFormModel extends BridgeFormModel
{
    private string $email = '';

    #[Bridge]
    public function getEmail(): string
    {
        return $this->email;
    }

    #[Bridge]
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function rules(): array
    {
        return [];
    }

    public function getRules(): array
    {
        return $this->rules();
    }
}
