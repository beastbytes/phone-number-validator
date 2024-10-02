<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;

$n6lPhoneNumberData = new N6lPhoneNumberData();

return [
    'array i11l EPP' => [
        [1, 2, 3],
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'array i11l ITU' => [
        [1, 2, 3],
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'array n6l' => [
        [1, 2, 3],
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'bool false i11l EPP' => [
        false,
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'bool true i11l EPP' => [
        true,
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'bool false i11l ITU' => [
        false,
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'bool true i11l ITU' => [
        true,
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'bool false n6l' => [
        false,
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'bool true n6l' => [
        true,
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'integer i11l EPP' => [
        random_int(PHP_INT_MIN, PHP_INT_MAX),
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'integer i11l ITU' => [
        random_int(PHP_INT_MIN, PHP_INT_MAX),
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'integer n6l' => [
        random_int(PHP_INT_MIN, PHP_INT_MAX),
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'float i11l EPP' => [
        random_int(PHP_INT_MIN, PHP_INT_MAX) / 100,
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'float i11l ITU' => [
        random_int(PHP_INT_MIN, PHP_INT_MAX) / 100,
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'float n6l' => [
        random_int(PHP_INT_MIN, PHP_INT_MAX) / 100,
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'function i11l EPP' => [
        fn() => random_int(PHP_INT_MIN, PHP_INT_MAX),
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'function i11l ITU' => [
        fn() => random_int(PHP_INT_MIN, PHP_INT_MAX),
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'function n6l' => [
        fn() => random_int(PHP_INT_MIN, PHP_INT_MAX),
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'object i11l EPP' => [
        new stdClass(),
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
    ],
    'object i11l ITU' => [
        new stdClass(),
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
    ],
    'object n6l' => [
        new stdClass(),
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
];