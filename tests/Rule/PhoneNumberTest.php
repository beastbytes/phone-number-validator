<?php

declare(strict_types=1);

namespace BeastBytes\PhoneNumber\Validator\Tests\Rule;

use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;
use BeastBytes\PhoneNumber\Validator\Tests\assets\NotPhoneNumber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Validator;

final class PhoneNumberTest extends TestCase
{
    public const INVALID_COUNTRY = 'ZZ';

    #[DataProvider('invalidConstructor')]
    public function test_invalid_constructor(string $exception, string $message, array $args): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);
        new PhoneNumber(...$args);
    }

    public function test_name(): void
    {
        $rule = new PhoneNumber(i11l: true);
        $this->assertSame(PhoneNumber::class, $rule->getName());
    }

    public function test_bad_rule(): void
    {
        $this->expectException(UnexpectedRuleException::class);
        (new Validator())->validate('01234 567890', new NotPhoneNumber());
    }

    #[DataProvider('options')]
    public function test_options(PhoneNumber $rule, array $expectedOptions): void
    {
        $this->assertSame($expectedOptions, $rule->getOptions());
    }

    #[DataProvider('phoneNumberTypeValidationFailed')]
    public function test_phone_number_type_validation_failed(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertFalse($result->isValid());
        $this->assertSame(
            strtr(PhoneNumber::INCORRECT_INPUT_MESSAGE, [
                '{type}' => get_debug_type($data)
            ]),
            $result->getErrorMessagesIndexedByPath()['phoneNumber'][0]
        );
    }

    #[DataProvider('i11lValidationPassed')]
    public function test_i11l_validation_passed(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertTrue($result->isValid());
    }

    #[DataProvider('i11lValidationFailed')]
    public function test_i11l_validation_failed(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertFalse($result->isValid());
        $this->assertSame(
            strtr(PhoneNumber::INVALID_I11L_PHONE_NUMBER_MESSAGE, [
                '{value}' => $data
            ]),
            $result->getErrorMessagesIndexedByPath()['phoneNumber.i11l'][0]
        );
    }

    #[DataProvider('n6lValidationPassed')]
    public function test_n6l_validation_passed(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertTrue($result->isValid());
    }

    #[DataProvider('n6lValidationFailed')]
    public function test_n6l_validation_failed(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertFalse($result->isValid());
        $this->assertSame(
            strtr(PhoneNumber::INVALID_N6L_PHONE_NUMBER_MESSAGE, [
                '{value}' => $data
            ]),
            $result->getErrorMessagesIndexedByPath()['phoneNumber.n6l'][0]
        );
    }

    // End tests //

    public static function getBadI11lFormat(): string
    {
        $chars = ['QWERTYUIOPASDFGHJKLZXCVBNM'];
        $length = random_int(0, 10);
        $i11lFormat = '';

        do {
            for ($i = 0; $i < $length; $i++) {
                $i11lFormat .= $chars[array_rand($chars)];
            }
        } while (in_array($i11lFormat, [PhoneNumberHandler::I11L_FORMAT_EPP, PhoneNumberHandler::I11L_FORMAT_ITU]));

        return $i11lFormat;
    }

    private function getErrorMessages(Result $result): array
    {
        return array_map(
            static fn(array $errors) => array_map(
                static fn(string $error) => str_replace(' ', ' ', $error),
                $errors
            ),
            $result->getErrorMessagesIndexedByPath(),
        );
    }

    private function getRuleClass(): string
    {
        return PhoneNumber::class;
    }

    // Data Providers //

    public static function invalidConstructor(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/InvalidConstructor.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }

    public static function options(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/Options.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }

    public static function phoneNumberTypeValidationFailed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/PhoneNumberTypeValidationFailed.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }

    public static function i11lValidationPassed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/I11lValidationPassed.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }

    public static function i11lValidationFailed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/I11lValidationFailed.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }

    public static function n6lValidationPassed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/N6lValidationPassed.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }

    public static function n6lValidationFailed(): \Generator
    {
        /** @var array<string, array> $data */
        $data = require dirname(__DIR__) . '/assets/N6lValidationFailed.php';

        foreach ($data as $name => $datum) {
            yield $name => $datum;
        }
    }
}
