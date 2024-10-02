<?php
/**
 * @copyright Copyright (c) 2024 BeastBytes - All Rights Reserved
 * @license BSD 3-Clause
 */

declare(strict_types=1);

namespace BeastBytes\PhoneNumber\Validator\Rule;

use Exception;
use RuntimeException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks that the value is a valid phone number.
 *
 * The phone number can be validated against national formats and/or as an international phone number in
 * {@link https://www.rfc-editor.org/rfc/rfc4933.html#section-2.5 Extensible Provisioning Protocol (EPP)} and/or
 * {@link https://www.itu.int/rec/T-REC-E.123 ITU-T Recommendation E.123} format. EPP format is also valid E.123
 */
final class PhoneNumberHandler implements RuleHandlerInterface
{
    public const COUNTRIES_NONE = false;
    /**
     * {@link https://www.rfc-editor.org/rfc/rfc4933.html#section-2.5 Extensible Provisioning Protocol (EPP)
     * Contact Mapping Telephone Numbers}
     */
    public const I11L_FORMAT_EPP = 'EPP';
    /**
     * {@link https://www.itu.int/rec/T-REC-E.123 ITU-T Recommendation E.123
     * (“Notation for national and i11l telephone numbers, e-mail addresses and Web addresses”)} and
     * {@link https://www.itu.int/rec/T-REC-E.164 ITU-T Recommendation E.164
     * (“The international public telecommunication numbering plan”)}
     */
    public const I11L_FORMAT_ITU = 'ITU';
    public const I11L_FORMAT_NONE = false;

    /**
     * @var string Regex pattern to match EPP format phone numbers
     */
    private const EPP_PATTERN = '/^\+\d{1,3}\.\d{4,14}(?:x.+)?$/';
    /**
     * @var string Regex pattern to match ITU format phone numbers (also matches, but does not ensure, EPP numbers)
     */
    private const ITU_PATTERN = '/^\+\d{1,3}[-. ](\d{2,6}([-. ])?){1,4}(?:([x#]).+)?$/';
    /**
     * @var int ITU-T Recommendation E.164 maximum international phone number length - excluding any extension
     */
    private const MAX_I11L = 15;
    /**
     * @var string Regex pattern used to remove the extension from phone numbers
     */
    private const REMOVE_EXT_PATTERN = '/^(.+?)([x#].+)?$/';

    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof PhoneNumber) {
            throw new UnexpectedRuleException(PhoneNumber::class, $rule);
        }

        $result = new Result();
        if (!is_string($value)) {
            $result->addError(
                $rule->getIncorrectInputMessage(),
                [
                    'attribute' => $context->getTranslatedProperty(),
                    'type' => get_debug_type($value),
                ],
                ['phoneNumber']
            );

            return $result;
        }

        $validI11l = $validN6l = false;
        if ($rule->getCountries() !== false) {
            $validN6l = $this->isValidN6l($value, $rule);
        }

        if (!$validN6l && $rule->getI11l() !== false) {
            $validI11l = $this->isValidI11l($value, $rule);
        }

        if (!$validI11l && !$validN6l) {
            if ($rule->getCountries() !== false) {
                $result->addError(
                    $rule->getInvalidN6lPhoneNumberMessage(),
                    [
                        'attribute' => $context->getTranslatedProperty(),
                        'value' => $value,
                    ],
                    ['phoneNumber', 'n6l']
                );
            }

            if ($rule->getI11l() !== false) {
                $result->addError(
                    $rule->getInvalidI11lPhoneNumberMessage(),
                    [
                        'attribute' => $context->getTranslatedProperty(),
                        'value' => $value,
                    ],
                    ['phoneNumber', 'i11l']
                );
            }
        }

        return $result;
    }

    /**
     * @param string $value Phone number to validate
     * @return bool Whether the phone number is a valid international phone number
     * @throws RuntimeException
     */
    protected function isValidI11l(string $value, PhoneNumber $rule): bool
    {
        $isI11l = false;

        if ($rule->getI11l() === self::I11L_FORMAT_ITU) {
            $isI11l = preg_match(self::ITU_PATTERN, $value) === 1;
        } elseif ($rule->getI11l() === self::I11L_FORMAT_EPP) {
            $isI11l = preg_match(self::EPP_PATTERN, $value) === 1;
        }

        if ($isI11l) {
            // Make sure the number is not too long
            $value = preg_replace(self::REMOVE_EXT_PATTERN, '$1', $value); // remove any extension
            $value = preg_replace('/\D/', '$1', $value); // remove non-digit characters
            return !(strlen($value) > self::MAX_I11L);
        }

        return false;
    }

    /**
     * @param string $value Phone number to validate
     * @return bool Whether the phone number is a valid national phone number
     * @throws RuntimeException
     * @psalm-suppress PossiblyInvalidIterator, PossiblyNullReference
     */
    protected function isValidN6l(string $value, PhoneNumber $rule): bool
    {
        $n6lPhoneNumberData = $rule->getN6lPhoneNumberData();

        /** @var string $country */
        foreach ($rule->getCountries() as $country) {
            /** @psalm-var array<non-empty-string, non-empty-string> $n6l */
            $n6l = $n6lPhoneNumberData->getN6l($country);

            if (preg_match($n6l['pattern'], $value)) {
                return true;
            }
        }

        return false;
    }
}
