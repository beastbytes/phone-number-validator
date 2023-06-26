<?php
/**
 * @copyright Copyright (c) 2023 BeastBytes - All Rights Reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PhoneNumber\Validator\Rule;

use BeastBytes\PhoneNumber\N6l\N6lPhoneNumberDataInterface;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

final class PhoneNumber implements RuleWithOptionsInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public const INCORRECT_INPUT_MESSAGE = 'Invalid type: "{type}". Phone number must be a string.';
    public const INVALID_I11L_FORMAT_MESSAGE = 'Invalid international format; must be "EPP" or "ITU".';
    public const INVALID_I11L_PHONE_NUMBER_MESSAGE = '{value} is not a valid international phone number.';
    public const INVALID_N6L_PHONE_NUMBER_MESSAGE = '{value} is not a valid national phone number.';
    public const NAME = 'phoneNumber';
    public const COUNTRIES_OR_I11L_NOT_FALSE_EXCEPTION_MESSAGE
        = 'At least one of $countries or $i11l must not be false';
    public const N6L_DATA_NOT_NULL_EXCEPTION_MESSAGE
        = '$n6lPhoneNumberData cannot be null if $countries is not false';
    public const COUNTRIES_NOT_FALSE_EXCEPTION_MESSAGE
        = '$countries cannot be false if $n6lPhoneNumberData is not null';
    public const INVALID_COUNTRY_EXCEPTION_MESSAGE = '"{country}" is not a valid country';

    /**
     * @param null|N6lPhoneNumberDataInterface $n6lPhoneNumberData Implementation of N6lPhoneNumberDataInterface
     * if validating national phone numbers; __*null*__ if not validating national phone numbers
     *
     * @param bool|string|string[] $countries Defines the country formats to validate against
     * * __*false*__: national phone numbers are not allowed
     * * __*true*__: validate against all countries defined by $n6lPhoneNumberData
     * * array: list of countries to validate against
     * * string: country to validate against
     * @see PhoneNumber::i11l
     * @see PhoneNumber::n6lPhoneNumberData
     *
     * @param bool|string $i11l How to handle international format phone numbers
     * * boolean __*false*__: international phone numbers are not allowed
     * * `EPP`: international phone numbers must be in Extensible Provisioning Protocol (EPP) format,
     * i.e `C{1,3}.N{,14}(xE+)?` where C is the 1 to 3 digit country code, N the national significant number up to 14
     * digits, and E the optional extension number
     * * `ITU`: international phone numbers must be in ITU-T Recommendation E.123 format;
     * i.e. groups of numbers, the separator may be a space, dot, or dash. The minimum grouping is:
     * <country code><separator><national significant number><(x|#)optional extension>; grouping of the national
     * significant number is supported, e.g. +44 1234 567 890
     * NOTE: `ITU` accepts EPP format numbers
     * @see PhoneNumberValidator::countries
     *
     * @param string $incorrectInputMessage Error message if value to validate is not a string
     * @param string $invalidI11lPhoneNumberMessage Error message if value is not a valid international phone number
     * @param string $invalidN6lPhoneNumberMessage Error message if value is not a valid national phone number
     */

    public function __construct(
        private ?N6lPhoneNumberDataInterface $n6lPhoneNumberData = null,
        private bool|array|string $countries = PhoneNumberHandler::COUNTRIES_NONE,
        private bool|string $i11l = PhoneNumberHandler::I11L_FORMAT_NONE,
        private string $incorrectInputMessage = self::INCORRECT_INPUT_MESSAGE,
        private string $invalidI11lFormatMessage = self::INVALID_I11L_FORMAT_MESSAGE,
        private string $invalidI11lPhoneNumberMessage = self::INVALID_I11L_PHONE_NUMBER_MESSAGE,
        private string $invalidN6lPhoneNumberMessage = self::INVALID_N6L_PHONE_NUMBER_MESSAGE,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null $when
         */
        private ?Closure $when = null,
    ) {
        if ($this->countries !== false && $this->n6lPhoneNumberData === null) {
            throw new InvalidArgumentException(self::N6L_DATA_NOT_NULL_EXCEPTION_MESSAGE);
        }

        if ($this->countries === false && $this->n6lPhoneNumberData !== null) {
            throw new InvalidArgumentException(self::COUNTRIES_NOT_FALSE_EXCEPTION_MESSAGE);
        }

        if (is_string($this->countries)) {
            $this->countries = [$this->countries];
        } elseif ($this->countries === true) {
            $this->countries = $this->n6lPhoneNumberData->getCountries();
        }

        if (is_array($this->countries)) {
            foreach ($this->countries as $country) {
                if (!$this->n6lPhoneNumberData->hasCountry($country)) {
                    throw new InvalidArgumentException(strtr(
                        self::INVALID_COUNTRY_EXCEPTION_MESSAGE,
                        ['{country}' => $country]
                    ));
                }
            }
        } elseif ($this->i11l === false) {
            throw new InvalidArgumentException(self::COUNTRIES_OR_I11L_NOT_FALSE_EXCEPTION_MESSAGE);
        }

        if (is_string($this->i11l)) {
            $this->i11l = strtoupper($this->i11l);

            if (!in_array($this->i11l, [
                PhoneNumberHandler::I11L_FORMAT_EPP,
                PhoneNumberHandler::I11L_FORMAT_ITU
            ], true)) {
                throw new InvalidArgumentException(self::INVALID_I11L_FORMAT_MESSAGE);
            }
        }
    }

    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getInvalidI11lPhoneNumberMessage(): string
    {
        return $this->invalidI11lPhoneNumberMessage;
    }

    public function getInvalidN6lPhoneNumberMessage(): string
    {
        return $this->invalidN6lPhoneNumberMessage;
    }

    public function getCountries(): bool|array
    {
        return $this->countries;
    }

    public function getI11l(): bool|string
    {
        return $this->i11l;
    }

    public function getN6lPhoneNumberData(): N6lPhoneNumberDataInterface
    {
        return $this->n6lPhoneNumberData;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    #[ArrayShape([
        'countries' => 'array',
        'i11l' => 'string',
        'incorrectInputMessage' => 'array',
        'invalidI11lPhoneNumberMessage' => 'array',
        'invalidN6lPhoneNumberMessage' => 'array',
        'n6lPhoneNumberData' => N6lPhoneNumberDataInterface::class,
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'countries' => $this->countries,
            'i11l' => $this->i11l,
            'incorrectInputMessage' => [
                'message' => $this->incorrectInputMessage,
            ],
            'invalidI11lPhoneNumberMessage' => [
                'message' => $this->invalidI11lPhoneNumberMessage,
            ],
            'invalidN6lPhoneNumberMessage' => [
                'message' => $this->invalidN6lPhoneNumberMessage,
            ],
            'n6lPhoneNumberData' => $this->n6lPhoneNumberData,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return PhoneNumberHandler::class;
    }
}
