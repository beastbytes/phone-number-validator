<?php

declare(strict_types=1);

namespace BeastBytes\PhoneNumber\Validator\Tests\Rule;

use BeastBytes\PhoneNumber\N6l\PHP\N6lPhoneNumberData;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumber;
use BeastBytes\PhoneNumber\Validator\Rule\PhoneNumberHandler;
use BeastBytes\PhoneNumber\Validator\Tests\assets\NotPhoneNumber;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\Validator;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

final class PhoneNumberTest extends TestCase
{
    private const INVALID_COUNTRY = 'ZZ';

    #[DataProvider('badConstructorProvider')]
    public function test_bad_constuctor(string $exception, string $message, array $args): void
    {
        $this->expectException($exception);
        $this->expectExceptionMessage($message);
        new PhoneNumber(...$args);
    }

    public function test_name(): void
    {
        $rule = new PhoneNumber(i11l: true);
        $this->assertSame(PhoneNumber::NAME, $rule->getName());
    }

    public function test_bad_rule(): void
    {
        $this->expectException(UnexpectedRuleException::class);
        (new Validator())->validate('01234 567890', new NotPhoneNumber());
    }

    #[DataProvider('optionsProvider')]
    public function test_options(RuleWithOptionsInterface $rule, array $expectedOptions): void
    {
        $this->assertSame($expectedOptions, $rule->getOptions());
    }

    #[DataProvider('invalidPhoneNumberTypeProvider')]
    public function test_invalid_phone_number_types_fail_validation(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertNotEmpty($result->getErrorMessagesIndexedByPath());
        $this->assertSame(
            strtr(PhoneNumber::INCORRECT_INPUT_MESSAGE, [
                '{type}' => get_debug_type($data)
            ]),
            $result->getErrorMessagesIndexedByPath()[''][0]
        );
    }

    #[DataProvider('validI11lPhoneNumberProvider')]
    public function test_valid_i11l_phone_numbers_pass_validation(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertEmpty($result->getErrorMessagesIndexedByPath());
    }

    #[DataProvider('invalidI11lPhoneNumberProvider')]
    public function test_invalid_i11l_phone_numbers_fail_validation(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertNotEmpty($result->getErrorMessagesIndexedByPath());
        $this->assertSame(
            strtr(PhoneNumber::INVALID_I11L_PHONE_NUMBER_MESSAGE, [
                '{value}' => $data
            ]),
            $result->getErrorMessagesIndexedByPath()[''][0]
        );
    }

    #[DataProvider('validN6lPhoneNumberProvider')]
    public function test_valid_n6l_phone_numbers_pass_validation(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertEmpty($result->getErrorMessagesIndexedByPath());
    }

    #[DataProvider('invalidN6lPhoneNumberProvider')]
    public function test_invalid_n6l_phone_numbers_fail_validation(mixed $data, PhoneNumber $rule): void
    {
        $result = (new Validator())->validate($data, $rule);
        $this->assertNotEmpty($result->getErrorMessagesIndexedByPath());
        $this->assertSame(
            strtr(PhoneNumber::INVALID_N6L_PHONE_NUMBER_MESSAGE, [
                '{value}' => $data
            ]),
            $result->getErrorMessagesIndexedByPath()[''][0]
        );
    }

    public static function badConstructorProvider(): \Generator
    {
        foreach ([
            'neither countries or iiil defined' => [
                InvalidArgumentException::class,
                PhoneNumber::COUNTRIES_OR_I11L_NOT_FALSE_EXCEPTION_MESSAGE,
                []
            ],
            'invalid i11l format' => [
                InvalidArgumentException::class,
                PhoneNumber::INVALID_I11L_FORMAT_MESSAGE,
                ['i11l' => self::getBadI11lFormat()]
            ],
            'n6l data not defined' => [
                InvalidArgumentException::class,
                PhoneNumber::N6L_DATA_NOT_NULL_EXCEPTION_MESSAGE,
                ['countries' => true]
            ],
            'countries not defined' => [
                InvalidArgumentException::class,
                PhoneNumber::COUNTRIES_NOT_FALSE_EXCEPTION_MESSAGE,
                ['n6lPhoneNumberData' => new N6lPhoneNumberData()]
            ],
            'invalid country in array' => [
                InvalidArgumentException::class,
                strtr(
                    PhoneNumber::INVALID_COUNTRY_EXCEPTION_MESSAGE,
                    ['{country}' => self::INVALID_COUNTRY]
                ),
                [
                    'countries' => ['GB', 'UA', self::INVALID_COUNTRY],
                    'n6lPhoneNumberData' => new N6lPhoneNumberData()
                ]
            ],
            'invalid country as string' => [
                InvalidArgumentException::class,
                strtr(
                    PhoneNumber::INVALID_COUNTRY_EXCEPTION_MESSAGE,
                    ['{country}' => self::INVALID_COUNTRY]
                ),
                [
                    'countries' => self::INVALID_COUNTRY,
                    'n6lPhoneNumberData' => new N6lPhoneNumberData()
                ]
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    public static function optionsProvider(): \Generator
    {
        foreach ([
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
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    public static function invalidPhoneNumberTypeProvider(): \Generator
    {
        foreach ([
            'array i11l EPP' => [
                [1, 2, 3],
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'array i11l ITU' => [
                [1, 2, 3],
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'array n6l' => [
                [1, 2, 3],
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'bool i11l EPP' => [
                random_int(0, 9) / 2 === 0,
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'bool i11l ITU' => [
                random_int(0, 9) / 2 === 0,
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'bool n6l' => [
                random_int(0, 9) / 2 === 0,
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'integer i11l EPP' => [
                random_int(PHP_INT_MIN, PHP_INT_MAX),
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'integer i11l ITU' => [
                random_int(PHP_INT_MIN, PHP_INT_MAX),
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'integer n6l' => [
                random_int(PHP_INT_MIN, PHP_INT_MAX),
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'float i11l EPP' => [
                random_int(PHP_INT_MIN, PHP_INT_MAX) / 100,
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'float i11l ITU' => [
                random_int(PHP_INT_MIN, PHP_INT_MAX) / 100,
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'float n6l' => [
                random_int(PHP_INT_MIN, PHP_INT_MAX) / 100,
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'function i11l EPP' => [
                fn() => random_int(PHP_INT_MIN, PHP_INT_MAX),
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'function i11l ITU' => [
                fn() => random_int(PHP_INT_MIN, PHP_INT_MAX),
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'function n6l' => [
                fn() => random_int(PHP_INT_MIN, PHP_INT_MAX),
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'object i11l EPP' => [
                new stdClass(),
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'object i11l ITU' => [
                new stdClass(),
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'object n6l' => [
                new stdClass(),
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    public static function validI11lPhoneNumberProvider(): \Generator
    {
        foreach ([
            'GB EPP' => [
                '+44.2087712924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'US EPP' => [
                '+1.2123340611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'GB EPP with extension #' => [
                '+44.2087712924x543',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'US EPP with extension #' => [
                '+1.2123340611x06660',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'GB ITU' => [
                '+44 208 771 2924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US ITU' => [
                '+1 212 334 0611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'GB ITU dot' => [
                '+44.208.771.2924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US ITU dot' => [
                '+1.212.334.0611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'GB ITU dash' => [
                '+44-208-771-2924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US ITU dash' => [
                '+1-212-334-0611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'GB ITU with extension #' => [
                '+44 208 771 2924#5434',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US ITU with extension #' => [
                '+1 212 334 0611#06660',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            // EPP format numbers are valid ITU format numbers
            'GB EPP:ITU' => [
                '+44.2087712924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US EPP:ITU' => [
                '+1.2123340611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'GB EPP:ITU with extension #' => [
                '+44.2087712924x5434',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US EPP:ITU with extension #' => [
                '+1.2123340611x06660',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    public static function invalidI11lPhoneNumberProvider(): \Generator
    {
        foreach ([
            'EPP no +' => [
                '442087712924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'ITU no +' => [
                '44 208 771 2924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'EPP to long' => [
                '+4420877129242087712924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'ITU to long' => [
                '+44 208 771 2924 208 771 2924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'GB ITU:EPP' => [
                '+44 208 771 2924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'US ITU:EPP' => [
                '+1 212 334 0611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'GB N6L:EPP' => [
                '208 7712 924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'US N6L:EPP' => [
                '(212) 3340 611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_EPP)
            ],
            'GB N6L:ITU' => [
                '208 7712 924',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
            'US N6L:ITU' => [
                '(212) 3340 611',
                new PhoneNumber(i11l: PhoneNumberHandler::I11L_FORMAT_ITU)
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    public static function validN6lPhoneNumberProvider(): \Generator
    {
        foreach ([
            'US' => [
                '(212) 334 0611',
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'US dot' => [
                '(212).334.0611',
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'US dash' => [
                '(212)-334-0611',
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'US:US' => [
                '(212) 334 0611',
                new PhoneNumber(countries: 'US', n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'US:[GB,US]' => [
                '(212) 334 0611',
                new PhoneNumber(countries: ['GB', 'US'], n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    public static function invalidN6lPhoneNumberProvider(): \Generator
    {
        foreach ([
            'I11l EPP' => [
                '+1.2123340611',
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'I11l EPP with extension #' => [
                '+1.2123340611x06660',
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'US bad separator' => [
                '(212)#334#0611',
                new PhoneNumber(countries: true, n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
            'US:GB' => [
                '(212) 334 0611',
                new PhoneNumber(countries: 'GB', n6lPhoneNumberData: new N6lPhoneNumberData())
            ],
        ] as $name => $data) {
            yield $name => $data;
        }
    }

    protected function getRuleClass(): string
    {
        return PhoneNumber::class;
    }

    private static function getBadI11lFormat()
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
}
