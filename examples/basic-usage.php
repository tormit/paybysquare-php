<?php

require __DIR__ . '/../vendor/autoload.php';

use PayBySquare\PayBySquare;

// Create a new PayBySquare instance with payment attributes
// You can optionally specify the path to the XZ binary as a second parameter:
// $payBySquare = new PayBySquare([...], '/path/to/xz');
$payBySquare = new PayBySquare([
    'amount' => 15.00,
    'currencyCode' => 'EUR',
    'iban' => 'SK2483300000002403097934',
    'bic' => 'FIOZSKBAXXX',
    'paymentDueDate' => '20250301',  // Format: YYYYMMDD
    // You can use either symbols or reference, but not both
    // 'constantSymbol' => '0308',
    'reference' => 'REF123456789',  // Payment reference (alternative to symbols)
    'paymentNote' => 'MMSR-2025-V0SU9'
]);

// Generate QR code and save to file
$payBySquare->saveQrCode(__DIR__ . '/payment-qr.png');

// Get the PAY by square data string (for custom QR code generation)
$payBySquareData = $payBySquare->getPayBySquareData();
echo "PAY by square data string: " . $payBySquareData . PHP_EOL;

// You can also convert the PayBySquare object directly to a string
echo "PAY by square data string (using __toString): " . $payBySquare . PHP_EOL;

echo "QR code saved to: " . __DIR__ . '/payment-qr.png' . PHP_EOL;