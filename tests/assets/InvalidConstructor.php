<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Tests\Rule\PhoneNumberTest;

$n6lPhoneNumberData = new N6lPhoneNumberData();

return [
    'neither countries or iiil defined' => [
        InvalidArgumentException::class,
        PhoneNumber::COUNTRIES_OR_I11L_NOT_FALSE_EXCEPTION_MESSAGE,
        []
    ],
    'invalid i11l format' => [
        InvalidArgumentException::class,
        PhoneNumber::INVALID_I11L_FORMAT_MESSAGE,
        ['i11l' => PhoneNumberTest::getBadI11lFormat()]
    ],
    'n6l data not defined' => [
        InvalidArgumentException::class,
        PhoneNumber::N6L_DATA_NOT_NULL_EXCEPTION_MESSAGE,
        ['countries' => true]
    ],
    'countries not defined' => [
        InvalidArgumentException::class,
        PhoneNumber::COUNTRIES_NOT_FALSE_EXCEPTION_MESSAGE,
        ['n6lPhoneNumberData' => $n6lPhoneNumberData]
    ],
    'invalid country in array' => [
        InvalidArgumentException::class,
        strtr(
            PhoneNumber::INVALID_COUNTRY_EXCEPTION_MESSAGE,
            ['{country}' => PhoneNumberTest::INVALID_COUNTRY]
        ),
        [
            'countries' => ['GB', 'UA', PhoneNumberTest::INVALID_COUNTRY],
            'n6lPhoneNumberData' => $n6lPhoneNumberData
        ]
    ],
    'invalid country as string' => [
        InvalidArgumentException::class,
        strtr(
            PhoneNumber::INVALID_COUNTRY_EXCEPTION_MESSAGE,
            ['{country}' => PhoneNumberTest::INVALID_COUNTRY]
        ),
        [
            'countries' => PhoneNumberTest::INVALID_COUNTRY,
            'n6lPhoneNumberData' => $n6lPhoneNumberData
        ]
    ],
];