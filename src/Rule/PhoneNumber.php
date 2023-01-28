<?php
/**
 * @copyright Copyright (c) 2021 BeastBytes - All Rights Reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PhoneNumber\Validator\Rule;

use BeastBytes\PhoneNumber\N6l\N6lPhoneNumberDataInterface;
use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use Closure;
use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

final class PhoneNumber implements SerializableRuleInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var string {@link https://www.rfc-editor.org/rfc/rfc4933.html#section-2.5 Extensible Provisioning Protocol (EPP)
     * Contact Mapping Telephone Numbers}
     */
    public const EPP_FORMAT = 'EPP';
    /**
     * @var string {@link https://www.itu.int/rec/T-REC-E.123 ITU-T Recommendation E.123
     * (“Notation for national and i11l telephone numbers, e-mail addresses and Web addresses”)} and
     * {@link https://www.itu.int/rec/T-REC-E.164 ITU-T Recommendation E.164 (“The international public
     * telecommunication numbering plan”)}
     */
    public const ITU_FORMAT = 'ITU';

    /**
     * @var string Regex pattern to match EPP format phone numbers
     */
    private const EPP_PATTERN = '/^\+\d{1,3}\.\d{4,14}(?:x.+)?$/';
    /**
     * @var string Regex pattern to match ITU format phone numbers (also matches, but does not ensure, EPP numbers)
     */
    private const ITU_PATTERN = '/^\+\d{1,3}[-. ](\d{2,6}([-. ])?){1,4}(?:(x|#).+)?$/';
    /**
     * @var int ITU-T Recommendation E.164 maximum international phone number length - excluding any extension
     */
    private const MAX_I11L = 15;
    /**
     * @var string Regex pattern used to remove the extension from phone numbers
     */
    private const REMOVE_EXT_PATTERN = '/^(.+?)([a-zA-Z#].+)?$/';

    /**
     * @var bool|string|string[] $countries Defines the country formats to validate against
     *
     * * __*false*__: national phone numbers are not allowed
     * * __*true*__: validate against all countries defined by $n6lPhoneNumberData
     * * array: list of countries to validate against
     * * string: country to validate against
     * @see PhoneNumber::i11l
     * @see PhoneNumber::n6lPhoneNumberData
     */

    /**
     * @param bool|string $i11l How to handle international format phone numbers
     *
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
     */

    public function __construct(
        private ?N6lPhoneNumberDataInterface $n6lPhoneNumberData = null,
        private bool|array|string $countries = false,
        private bool|string $i11l = false,
        private string $invalidInternationalPhoneNumberMessage = 'Invalid international phone number',
        private string $invalidNationalPhoneNumberMessage = 'Invalid national phone number',
        /**
         * @var bool|callable|null $skipOnEmpty
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null $when
         */
        private ?Closure $when = null,
    ) {
        if ($this->countries === false && $this->i11l === false) {
            throw new InvalidArgumentException('At least one of $countries or $i11l must not be false');
        }

        if ($this->countries !== false && $this->n6lPhoneNumberData === null) {
            throw new InvalidArgumentException('$n6lPhoneNumberData cannot be null if $countries is not false');
        }

        if ($this->countries === false && $this->n6lPhoneNumberData !== null) {
            throw new InvalidArgumentException('$countries cannot be false if $n6lPhoneNumberData is not null');
        }

        if (is_string($this->countries)) {
            $this->countries = (array)$this->countries;
        }

        if (is_array($this->countries)) {
            foreach ($this->countries as $country) {
                if (!$this->n6lPhoneNumberData->hasCountry($country)) {
                    throw new InvalidArgumentException(strtr(
                        '"{country}"  not a valid country',
                        ['{country}' => $country]
                    ));
                }
            }
        }
    }

    public function getInvalidInternationalPhoneNumberMessage(): string
    {
        return $this->invalidInternationalPhoneNumberMessage;
    }

    public function getInvalidNationalPhoneNumberMessage(): string
    {
        return $this->invalidNationalPhoneNumberMessage;
    }

    public function getCountries(): bool|array
    {
        if (is_null($this->countries)) {
            return $this->n6lPhoneNumberData->getCountries();
        }

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
        return 'phoneNumber';
    }

    #[ArrayShape([
        'countries' => 'array',
        'i11l' => 'string',
        'invalidInternationalPhoneNumberMessage' => 'array',
        'invalidNationalPhoneNumberMessage' => 'array',
        'n6lPhoneNumberData' => N6lPhoneNumberDataInterface::class,
        'skipOnEmpty' => 'bool',
        'skipOnError' => 'bool',
    ])]
    public function getOptions(): array
    {
        return [
            'countries' => $this->countries,
            'i11l' => $this->i11l,
            'invalidInternationalPhoneNumberMessage' => [
                'message' => $this->invalidInternationalPhoneNumberMessage,
            ],
            'invalidNationalPhoneNumberMessage' => [
                'message' => $this->invalidNationalPhoneNumberMessage,
            ],
            'n6lPhoneNumberData' => $this->n6lPhoneNumberData,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return PhoneNumberHandler::class;
    }
}
