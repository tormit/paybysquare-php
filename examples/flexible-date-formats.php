<?php

require __DIR__ . '/../vendor/autoload.php';

use PayBySquare\Models\Payment;

echo "PayBySquare - Flexible Date Format Example\n";
echo "==========================================\n\n";

// Create a new Payment instance
$payment = new Payment();

// Example 1: Using YYYYMMDD format (original format)
$payment->setPaymentDueDate('20250301');
echo "Example 1 - YYYYMMDD format:\n";
echo "Input: '20250301'\n";
echo "Internal DateTime: " . $payment->getPaymentDueDateObject()->format('Y-m-d') . "\n";
echo "Output (getPaymentDueDate): " . $payment->getPaymentDueDate() . "\n";
echo "Output (getFormattedPaymentDueDate): " . $payment->getFormattedPaymentDueDate() . "\n\n";

// Example 2: Using ISO format (YYYY-MM-DD)
$payment->setPaymentDueDate('2025-03-02');
echo "Example 2 - ISO format (YYYY-MM-DD):\n";
echo "Input: '2025-03-02'\n";
echo "Internal DateTime: " . $payment->getPaymentDueDateObject()->format('Y-m-d') . "\n";
echo "Output (getPaymentDueDate): " . $payment->getPaymentDueDate() . "\n";
echo "Output (getFormattedPaymentDueDate): " . $payment->getFormattedPaymentDueDate() . "\n\n";

// Example 3: Using common date format (MM/DD/YYYY)
$payment->setPaymentDueDate('03/03/2025');
echo "Example 3 - Common date format (MM/DD/YYYY):\n";
echo "Input: '03/03/2025'\n";
echo "Internal DateTime: " . $payment->getPaymentDueDateObject()->format('Y-m-d') . "\n";
echo "Output (getPaymentDueDate): " . $payment->getPaymentDueDate() . "\n";
echo "Output (getFormattedPaymentDueDate): " . $payment->getFormattedPaymentDueDate() . "\n\n";

// Example 4: Using DateTime object
$dateTime = new DateTime('2025-03-04');
$payment->setPaymentDueDate($dateTime);
echo "Example 4 - DateTime object:\n";
echo "Input: DateTime('2025-03-04')\n";
echo "Internal DateTime: " . $payment->getPaymentDueDateObject()->format('Y-m-d') . "\n";
echo "Output (getPaymentDueDate): " . $payment->getPaymentDueDate() . "\n";
echo "Output (getFormattedPaymentDueDate): " . $payment->getFormattedPaymentDueDate() . "\n\n";

// Example 5: Using timestamp
$timestamp = strtotime('2025-03-05');
$payment->setPaymentDueDate($timestamp);
echo "Example 5 - Timestamp:\n";
echo "Input: strtotime('2025-03-05')\n";
echo "Internal DateTime: " . $payment->getPaymentDueDateObject()->format('Y-m-d') . "\n";
echo "Output (getPaymentDueDate): " . $payment->getPaymentDueDate() . "\n";
echo "Output (getFormattedPaymentDueDate): " . $payment->getFormattedPaymentDueDate() . "\n\n";

// Example 6: Using natural language
$payment->setPaymentDueDate('next Monday');
echo "Example 6 - Natural language:\n";
echo "Input: 'next Monday'\n";
echo "Internal DateTime: " . $payment->getPaymentDueDateObject()->format('Y-m-d') . "\n";
echo "Output (getPaymentDueDate): " . $payment->getPaymentDueDate() . "\n";
echo "Output (getFormattedPaymentDueDate): " . $payment->getFormattedPaymentDueDate() . "\n\n";

echo "All examples show that regardless of the input format, the output is always in YYYYMMDD format.\n";