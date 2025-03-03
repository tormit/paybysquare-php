# PayBySquare PHP Library Tests

This directory contains PHPUnit tests for the PayBySquare PHP library.

## Test Structure

The tests are organized as follows:

- `Models/BankAccountTest.php`: Tests for the `BankAccount` model class
- `Models/PaymentTest.php`: Tests for the `Payment` model class
- `EncoderTest.php`: Tests for the `Encoder` class
- `EncoderPrivateMethodsTest.php`: Tests for the private methods of the `Encoder` class
- `PayBySquareTest.php`: Tests for the main `PayBySquare` class

## Running the Tests

To run the tests, you need to have PHPUnit installed. The library already includes PHPUnit as a dev dependency in `composer.json`, so you can install it by running:

```bash
composer install
```

Then, you can run the tests using the PHPUnit binary:

```bash
./vendor/bin/phpunit
```

Or, if you have PHPUnit installed globally:

```bash
phpunit
```

## Test Coverage

The tests cover all the main functionality of the library, including:

- Creating and manipulating `BankAccount` and `Payment` objects
- Encoding payment data into PAY by square format
- Generating QR codes in different formats (PNG, SVG)
- Saving QR codes to files
- Getting QR codes as data URIs or HTML tags

## Requirements

Some tests require specific PHP extensions:

- The `testSaveQRCodeImage` test requires the `gd` extension
- The `testSaveQRCodeImageWithSvgFormat` test requires the `dom` extension

If these extensions are not available, the corresponding tests will be skipped.

Additionally, the `Encoder` tests that use the `compressWithLzma` method require the XZ binary to be installed on the system. If the XZ binary is not available, these tests will be skipped.