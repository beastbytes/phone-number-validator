<?php

declare(strict_types=1);

namespace Tests\Rule;

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\RuleHandlerInterface;

final class PhoneNumberHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        return [
            'US national number - UK' => [
                new PhoneNumber(
                    n6lPhoneNumberData: new N6lPhoneNumberData(),
                    countries:          'GB'
                ),
                '202-456-2121',
                [new Error('Invalid national phone number')]
            ],
            'UK national number - US' => [
                new PhoneNumber(
                    n6lPhoneNumberData: new N6lPhoneNumberData(),
                    countries:          'US'
                ),
                '020 7925 0918',
                [new Error('Invalid national phone number')]
            ],
            'UK international number' => [
                new PhoneNumber(
                    n6lPhoneNumberData: new N6lPhoneNumberData(),
                    countries:          'GB'
                ),
                '+44 20 7925 0918',
                [new Error('Invalid national phone number')]
            ],
            'UK national number - EPP' => [
                new PhoneNumber(
                    i11l: PhoneNumber::EPP_FORMAT
                ),
                '020 7925 0918',
                [new Error('Invalid international phone number')]
            ],
            'UK national number - ITU' => [
                new PhoneNumber(
                    i11l: PhoneNumber::ITU_FORMAT
                ),
                '020 7925 0918',
                [new Error('Invalid international phone number')]
            ],
            'US national number - EPP' => [
                new PhoneNumber(
                    i11l: PhoneNumber::EPP_FORMAT
                ),
                '202-456-2121',
                [new Error('Invalid international phone number')]
            ],
            'US national number - ITU' => [
                new PhoneNumber(
                    i11l: PhoneNumber::ITU_FORMAT
                ),
                '202-456-2121',
                [new Error('Invalid international phone number')]
            ],
            'ITU format number' => [
                new PhoneNumber(
                    i11l: PhoneNumber::EPP_FORMAT
                ),
                '+44 20 7925 0918',
                [new Error('Invalid international phone number')]
            ],
        ];
    }

    public function passedValidationProvider(): array
    {
        $passedValidationProvider = [];

        foreach (['020-7925-0918', '0300 126 7000'] as $phoneNumber) {
            $rule = new PhoneNumber(
                n6lPhoneNumberData: new N6lPhoneNumberData(),
                countries: 'GB'
            );
            $passedValidationProvider[] = [$rule, $phoneNumber];
        }

        foreach (['202-456-2121'] as $phoneNumber) {
            $rule = new PhoneNumber(
                n6lPhoneNumberData: new N6lPhoneNumberData(),
                countries: 'US'
            );
            $passedValidationProvider[] = [$rule, $phoneNumber];
        }

        foreach (['+44.2079250918', '+1.2024562121'] as $phoneNumber) {
            $rule = new PhoneNumber(
                i11l: PhoneNumber::EPP_FORMAT
            );
            $passedValidationProvider[] = [$rule, $phoneNumber];
        }

        foreach (['+44 20 7925 0918', '+1 202-456-2121','+44.2079250918', '+1.2024562121'] as $phoneNumber) {
            $rule = new PhoneNumber(
                i11l: PhoneNumber::ITU_FORMAT
            );
            $passedValidationProvider[] = [$rule, $phoneNumber];
        }

        return $passedValidationProvider;
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [
                new PhoneNumber(
                    n6lPhoneNumberData: new N6lPhoneNumberData(),
                    countries: 'GB',
                    invalidNationalPhoneNumberMessage: 'Custom invalid national phone number message'
                ),
                '202-456-1111',
                [new Error('Custom invalid national phone number message')],
            ],
            [
                new PhoneNumber(
                    i11l: PhoneNumber::ITU_FORMAT,
                    invalidInternationalPhoneNumberMessage: 'Custom invalid international phone number message',
                ),
                '020 7925 0918',
                [new Error('Custom invalid international phone number message')],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new PhoneNumberHandler($this->getTranslator());
    }
}
