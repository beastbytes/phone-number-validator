# Phone Number Validator (phone-number-validator)
Provides validation for national and international phone numbers.

**NOTE:** phone-number-validator does _**not**_ guarantee that a phone number exists, just that it is in a valid format.

## International Phone Numbers
International phone numbers are validated against the [Extensible Provisioning Protocol (EPP)](https://www.rfc-editor.org/rfc/rfc4933.html#section-2.5) and/or [ITU-T Recommendation E.123 (“Notation for national and i11l telephone numbers, e-mail addresses and Web addresses”)](https://www.itu.int/rec/T-REC-E.123) and [ITU-T Recommendation E.164 (“The international public telecommunication numbering plan”)](https://www.itu.int/rec/T-REC-E.164) formats.

**Note:** EPP formatted phone numbers are valid ITU phone numbers. 

## National Phone Numbers
To validate national phone numbers a N6lPhoneNumberDataInterface implementation is required, e.g.
beastbytes/n6l-phone-number-data-php which provides all numbering plans in the
[ITU T0202 National Numbering Plans](https://www.itu.int/oth/T0202.aspx?parent=T0202)

National phone numbers are validated against country specific formats; they can be validated against a single country, a subset of, or all countries provided by the N6lPhoneNumberDataInterface implementation.

## License
For license information see the [LICENSE](LICENSE.md) file.

## Installation
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist beastbytes/phone-number-validator
```

or add

```json
"beastbytes/phone-number-validator": "^1.0.0"
```

to the 'require' section of your composer.json.


## Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

## Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```
