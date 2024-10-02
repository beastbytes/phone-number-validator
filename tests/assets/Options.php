<?php

declare(strict_types=1);

use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;

return [
    'i11l EPP' => [
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP),
        [
            'countries' => PhoneNumberHandler::COUNTRIES_NONE,
            'i11l' => PhoneNumberHandler::I11L_FORMAT_EPP,
            'incorrectInputMessage' => [
                'message' => PhoneNumber::INCORRECT_INPUT_MESSAGE,
            ],
            'invalidI11lPhoneNumberMessage' => [
                'message' => PhoneNumber::INVALID_I11L_PHONE_NUMBER_MESSAGE,
            ],
            'invalidN6lPhoneNumberMessage' => [
                'message' => PhoneNumber::INVALID_N6L_PHONE_NUMBER_MESSAGE,
            ],
            'n6lPhoneNumberData' => null,
            'skipOnEmpty' => false,
            'skipOnError' => false,
        ]
    ],
    'i11l ITU' => [
        new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU),
        [
            'countries' => PhoneNumberHandler::COUNTRIES_NONE,
            'i11l' => PhoneNumberHandler::I11L_FORMAT_ITU,
            'incorrectInputMessage' => [
                'message' => PhoneNumber::INCORRECT_INPUT_MESSAGE,
            ],
            'invalidI11lPhoneNumberMessage' => [
                'message' => PhoneNumber::INVALID_I11L_PHONE_NUMBER_MESSAGE,
            ],
            'invalidN6lPhoneNumberMessage' => [
                'message' => PhoneNumber::INVALID_N6L_PHONE_NUMBER_MESSAGE,
            ],
            'n6lPhoneNumberData' => null,
            'skipOnEmpty' => false,
            'skipOnError' => false,
        ]
    ],
];