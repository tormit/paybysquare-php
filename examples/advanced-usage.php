<?php

require __DIR__ . '/../vendor/autoload.php';

use PayBySquare\PayBySquare;
use PayBySquare\Models\Payment;
use PayBySquare\Models\BankAccount;
use PayBySquare\Exceptions\EncodingException;

// Create a new PayBySquare instance
// You can specify the path to the XZ binary if it's not in a standard location
// $payBySquare = new PayBySquare(null, '/custom/path/to/xz');
$payBySquare = new PayBySquare();

// Create a payment manually
$payment = new Payment();
$payment->setAmount(15.00);
$payment->setCurrencyCode('EUR');
$payment->setPaymentDueDate('20250301');

// You can use either payment symbols or payment reference, but not both
// Option 1: Use payment symbols
//$payment->setVariableSymbol('1234567890');
//$payment->setConstantSymbol('0308');
//$payment->setSpecificSymbol('54321');

// Option 2: Use payment reference (alternative to symbols)
$payment->setReference('REF123456789');

$payment->setPaymentNote('MMSR-2025-V0SU9');

// Add bank account
$bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
$payment->addBankAccount($bankAccount);

// Set beneficiary information
$payment->setBeneficiaryName('Company Name Ltd.');
$payment->setBeneficiaryAddressLine1('Street Name 123');
$payment->setBeneficiaryAddressLine2('12345 City, Country');

// Set the payment on the PayBySquare instance
$payBySquare->setPayment($payment);

// Generate QR code with custom options
$qrCodePng = $payBySquare->generateQrCode([
    'size' => 400,    // QR code size in pixels
    'margin' => 20    // QR code margin in pixels
]);

echo $payment->toTabDelimitedString(),"\n";

// Save to file
file_put_contents(__DIR__ . '/payment-qr-advanced.png', $qrCodePng);

// Get the PAY by square data string
$payBySquareData = $payBySquare->getPayBySquareData();
echo "PAY by square data string: " . $payBySquareData . PHP_EOL;

// You can also convert the PayBySquare object directly to a string
echo "PAY by square data string (using __toString): " . $payBySquare . PHP_EOL;

echo "QR code saved to: " . __DIR__ . '/payment-qr-advanced.png' . PHP_EOL;

// Example of how to handle errors
try {
    // This would fail if XZ binary is not installed or not found in common locations
    $payBySquareData = $payBySquare->getPayBySquareData();
} catch (EncodingException $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "You can specify the path to the XZ binary when creating the PayBySquare instance:" . PHP_EOL;
    echo '$payBySquare = new PayBySquare(null, "/path/to/xz");' . PHP_EOL;
}