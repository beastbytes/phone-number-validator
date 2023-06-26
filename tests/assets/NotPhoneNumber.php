<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PhoneNumber\Validator\Tests\assets;

use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;
use Yiisoft\Validator\RuleInterface;

final class NotPhoneNumber implements RuleInterface
{
    public function getHandler(): string
    {
        return PhoneNumberHandler::class;
    }

    public function getName(): string
    {
        return 'notPhoneNumber';
    }
}
