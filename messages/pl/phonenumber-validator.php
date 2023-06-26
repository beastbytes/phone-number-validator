<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;

return [
    PhoneNumber::INCORRECT_INPUT_MESSAGE => 'Nieprawidłowy typ: „{type}”. Numer telefonu musi być ciągiem znaków.',
    PhoneNumber::INVALID_I11L_PHONE_NUMBER_MESSAGE => '{value} nie jest prawidłowym międzynarodowym numerem telefonu.',
    PhoneNumber::INVALID_N6L_PHONE_NUMBER_MESSAGE => '{value} nie jest prawidłowym krajowym numerem telefonu.'
];
