<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;

return [
    'GB EPP' => [
        '+44.2087712924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'US EPP' => [
        '+1.2123340611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'GB EPP with extension #' => [
        '+44.2087712924x543',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'US EPP with extension #' => [
        '+1.2123340611x06660',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
    ],
    'GB ITU' => [
        '+44 208 771 2924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US ITU' => [
        '+1 212 334 0611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'GB ITU dot' => [
        '+44.208.771.2924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US ITU dot' => [
        '+1.212.334.0611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'GB ITU dash' => [
        '+44-208-771-2924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US ITU dash' => [
        '+1-212-334-0611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'GB ITU with extension #' => [
        '+44 208 771 2924#5434',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US ITU with extension #' => [
        '+1 212 334 0611#06660',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    // EPP format numbers are valid ITU format numbers
    'GB EPP:ITU' => [
        '+44.2087712924',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US EPP:ITU' => [
        '+1.2123340611',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'GB EPP:ITU with extension #' => [
        '+44.2087712924x5434',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
    'US EPP:ITU with extension #' => [
        '+1.2123340611x06660',
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
    ],
];