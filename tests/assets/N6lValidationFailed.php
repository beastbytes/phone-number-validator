<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;

$n6lPhoneNumberData = new N6lPhoneNumberData();

return [
    'I11l EPP' => [
        '+1.2123340611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'I11l EPP with extension #' => [
        '+1.2123340611x06660',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'US bad separator' => [
        '(212)#334#0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: true),
    ],
    'US:GB' => [
        '(212) 334 0611',
        new PhoneNumber(n6lPhoneNumberData: $n6lPhoneNumberData, countries: 'GB'),
    ],
];