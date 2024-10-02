<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;

$n6lPhoneNumberData = new N6lPhoneNumberData();

return [
    'US' => [
        '(212) 334 0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true)
    ],
    'US dot' => [
        '(212).334.0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true)
    ],
    'US dash' => [
        '(212)-334-0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true)
    ],
    'US:US' => [
        '(212) 334 0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: 'US')
    ],
    'US:[GB,US]' => [
        '(212) 334 0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: ['GB', 'US'])
    ],
];