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
 * Test FormModel for testing BridgeFormModel
 *
 * @copyright 2010-2026 Blackcube - Philippe Gaultier
 * @license https://www.blackcube.io/license
 * @link https://www.blackcube.io
 */
class TestFormModel extends BridgeFormModel
{
    #[Bridge]
    public string $name = '';

    #[Bridge]
    public ?int $age = null;

    #[Bridge]
    public bool $active = false;

    #[Bridge]
    public string $email = '';

    public function rules(): array
    {
        return [];
    }

    public function getRules(): array
    {
        return $this->rules();
    }
}
