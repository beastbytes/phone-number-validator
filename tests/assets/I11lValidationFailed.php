<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;

return [
    'EPP no +' => [
        '442087712924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'ITU no +' => [
        '44 208 771 2924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'EPP to long' => [
        '+4420877129242087712924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'ITU to long' => [
        '+44 208 771 2924 208 771 2924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'GB ITU:EPP' => [
        '+44 208 771 2924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'US ITU:EPP' => [
        '+1 212 334 0611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'GB N6L:EPP' => [
        '208 7712 924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'US N6L:EPP' => [
        '(212) 3340 611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'GB N6L:ITU' => [
        '208 7712 924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US N6L:ITU' => [
        '(212) 3340 611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
];