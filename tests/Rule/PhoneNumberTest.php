<?php
/**
 * @copyright Copyright © 2022 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace Tests\Rule;

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use Yiisoft\Validator\SerializableRuleInterface;

class PhoneNumberTest extends AbstractRuleTest
{
    public function optionsDataProvider(): array
    {
        return [
            [
                (new PhoneNumber(
                    new N6lPhoneNumberData(),
                    countries: ['GB', 'FR'],
                    i11l: PhoneNumber::EPP_FORMAT
                )),
                [
                    'invalidInternationalPhoneNumberMessage' => [
                        'message' => 'Invalid international phone number',
                    ],
                    'invalidNationalPhoneNumberMessage' => [
                        'message' => 'Invalid national phone number'
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'countries' => ['GB', 'FR'],
                    'i11l' => PhoneNumber::EPP_FORMAT,
                    'n6lPhoneNumberData' => new N6lPhoneNumberData()

                ],
            ],
        ];
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new PhoneNumber(i11l: PhoneNumber::ITU_FORMAT);
    }
}
