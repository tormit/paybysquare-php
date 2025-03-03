<?php

namespace PayBySquare\Tests\Models;

use PHPUnit\Framework\TestCase;
use PayBySquare\Models\Payment;
use PayBySquare\Models\BankAccount;

class PaymentTest extends TestCase
{
    /**
     * Test default values
     */
    public function testDefaultValues(): void
    {
        $payment = new Payment();
        
        $this->assertEquals('paymentorder', $payment->getPaymentOption());
        $this->assertNull($payment->getAmount());
        $this->assertEquals('EUR', $payment->getCurrencyCode());
        $this->assertNull($payment->getPaymentDueDate());
        $this->assertNull($payment->getVariableSymbol());
        $this->assertNull($payment->getConstantSymbol());
        $this->assertNull($payment->getSpecificSymbol());
        $this->assertNull($payment->getPaymentNote());
        $this->assertEmpty($payment->getBankAccounts());
        $this->assertNull($payment->getBeneficiaryName());
        $this->assertNull($payment->getBeneficiaryAddressLine1());
        $this->assertNull($payment->getBeneficiaryAddressLine2());
    }
    
    /**
     * Test setters and getters
     */
    public function testSettersAndGetters(): void
    {
        $payment = new Payment();
        
        // Test payment option
        $this->assertSame($payment, $payment->setPaymentOption('standingorder'));
        $this->assertEquals('standingorder', $payment->getPaymentOption());
        
        // Test amount
        $this->assertSame($payment, $payment->setAmount(123.45));
        $this->assertEquals(123.45, $payment->getAmount());
        
        // Test currency code
        $this->assertSame($payment, $payment->setCurrencyCode('USD'));
        $this->assertEquals('USD', $payment->getCurrencyCode());
        
        // Test payment due date
        $this->assertSame($payment, $payment->setPaymentDueDate('20250301'));
        $this->assertInstanceOf(\DateTimeInterface::class, $payment->getPaymentDueDateObject());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('20250301', $payment->getFormattedPaymentDueDate());
        
        // Test variable symbol
        $this->assertSame($payment, $payment->setVariableSymbol('12345'));
        $this->assertEquals('12345', $payment->getVariableSymbol());
        
        // Test constant symbol
        $this->assertSame($payment, $payment->setConstantSymbol('0308'));
        $this->assertEquals('0308', $payment->getConstantSymbol());
        
        // Test specific symbol
        $this->assertSame($payment, $payment->setSpecificSymbol('54321'));
        $this->assertEquals('54321', $payment->getSpecificSymbol());
        
        // Test payment note
        $this->assertSame($payment, $payment->setPaymentNote('Test payment'));
        $this->assertEquals('Test payment', $payment->getPaymentNote());
        
        // Test beneficiary name
        $this->assertSame($payment, $payment->setBeneficiaryName('John Doe'));
        $this->assertEquals('John Doe', $payment->getBeneficiaryName());
        
        // Test beneficiary address line 1
        $this->assertSame($payment, $payment->setBeneficiaryAddressLine1('123 Main St'));
        $this->assertEquals('123 Main St', $payment->getBeneficiaryAddressLine1());
        
        // Test beneficiary address line 2
        $this->assertSame($payment, $payment->setBeneficiaryAddressLine2('Anytown, USA'));
        $this->assertEquals('Anytown, USA', $payment->getBeneficiaryAddressLine2());
    }
    
    /**
     * Test invalid payment option
     */
    public function testInvalidPaymentOption(): void
    {
        $payment = new Payment();
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid payment option');
        
        $payment->setPaymentOption('invalidoption');
    }
    
    /**
     * Test bank account methods
     */
    public function testBankAccountMethods(): void
    {
        $payment = new Payment();
        $bankAccount1 = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $bankAccount2 = new BankAccount('CZ6508000000192000145399', 'GIBACZPX');
        
        // Test addBankAccount
        $this->assertSame($payment, $payment->addBankAccount($bankAccount1));
        $this->assertCount(1, $payment->getBankAccounts());
        $this->assertSame($bankAccount1, $payment->getBankAccounts()[0]);
        
        // Test setBankAccounts
        $this->assertSame($payment, $payment->setBankAccounts([$bankAccount1, $bankAccount2]));
        $this->assertCount(2, $payment->getBankAccounts());
        $this->assertSame($bankAccount1, $payment->getBankAccounts()[0]);
        $this->assertSame($bankAccount2, $payment->getBankAccounts()[1]);
    }
    
    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        $payment = new Payment();
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('USD');
        $payment->setPaymentDueDate('20250301');
        $payment->setVariableSymbol('12345');
        $payment->setConstantSymbol('0308');
        $payment->setSpecificSymbol('54321');
        $payment->setPaymentNote('Test payment');
        $payment->setBeneficiaryName('John Doe');
        $payment->setBeneficiaryAddressLine1('123 Main St');
        $payment->setBeneficiaryAddressLine2('Anytown, USA');
        
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $payment->addBankAccount($bankAccount);
        
        $array = $payment->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('paymentOption', $array);
        $this->assertArrayHasKey('amount', $array);
        $this->assertArrayHasKey('currencyCode', $array);
        $this->assertArrayHasKey('paymentDueDate', $array);
        $this->assertArrayHasKey('variableSymbol', $array);
        $this->assertArrayHasKey('constantSymbol', $array);
        $this->assertArrayHasKey('specificSymbol', $array);
        $this->assertArrayHasKey('paymentNote', $array);
        $this->assertArrayHasKey('bankAccounts', $array);
        $this->assertArrayHasKey('beneficiaryName', $array);
        $this->assertArrayHasKey('beneficiaryAddressLine1', $array);
        $this->assertArrayHasKey('beneficiaryAddressLine2', $array);
        
        $this->assertEquals('paymentorder', $array['paymentOption']);
        $this->assertEquals(123.45, $array['amount']);
        $this->assertEquals('USD', $array['currencyCode']);
        $this->assertEquals('20250301', $array['paymentDueDate']);
        $this->assertEquals('12345', $array['variableSymbol']);
        $this->assertEquals('0308', $array['constantSymbol']);
        $this->assertEquals('54321', $array['specificSymbol']);
        $this->assertEquals('Test payment', $array['paymentNote']);
        $this->assertEquals('John Doe', $array['beneficiaryName']);
        $this->assertEquals('123 Main St', $array['beneficiaryAddressLine1']);
        $this->assertEquals('Anytown, USA', $array['beneficiaryAddressLine2']);
        
        $this->assertIsArray($array['bankAccounts']);
        $this->assertCount(1, $array['bankAccounts']);
        $this->assertArrayHasKey('iban', $array['bankAccounts'][0]);
        $this->assertArrayHasKey('bic', $array['bankAccounts'][0]);
        $this->assertEquals('SK2483300000002403097934', $array['bankAccounts'][0]['iban']);
        $this->assertEquals('FIOZSKBAXXX', $array['bankAccounts'][0]['bic']);
        
        // Test with minimal data
        $minimalPayment = new Payment();
        $minimalPayment->setAmount(123.45);
        $minimalPayment->setCurrencyCode('USD');
        $minimalPayment->addBankAccount(new BankAccount('SK2483300000002403097934'));
        
        $minimalArray = $minimalPayment->toArray();
        
        $this->assertArrayHasKey('paymentOption', $minimalArray);
        $this->assertArrayHasKey('amount', $minimalArray);
        $this->assertArrayHasKey('currencyCode', $minimalArray);
        $this->assertArrayHasKey('bankAccounts', $minimalArray);
        $this->assertArrayNotHasKey('paymentDueDate', $minimalArray);
        $this->assertArrayNotHasKey('variableSymbol', $minimalArray);
        $this->assertArrayNotHasKey('constantSymbol', $minimalArray);
        $this->assertArrayNotHasKey('specificSymbol', $minimalArray);
        $this->assertArrayNotHasKey('paymentNote', $minimalArray);
        $this->assertArrayNotHasKey('beneficiaryName', $minimalArray);
        $this->assertArrayNotHasKey('beneficiaryAddressLine1', $minimalArray);
        $this->assertArrayNotHasKey('beneficiaryAddressLine2', $minimalArray);
    }
    
    /**
     * Test flexible payment due date formats
     */
    public function testFlexiblePaymentDueDateFormats(): void
    {
        $payment = new Payment();
        
        // Test with YYYYMMDD format
        $payment->setPaymentDueDate('20250301');
        $this->assertInstanceOf(\DateTimeInterface::class, $payment->getPaymentDueDateObject());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('20250301', $payment->getFormattedPaymentDueDate());
        
        // Test with ISO format (YYYY-MM-DD)
        $payment->setPaymentDueDate('2025-03-01');
        $this->assertInstanceOf(\DateTimeInterface::class, $payment->getPaymentDueDateObject());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('20250301', $payment->getFormattedPaymentDueDate());
        
        // Test with common date format (MM/DD/YYYY)
        $payment->setPaymentDueDate('03/01/2025');
        $this->assertInstanceOf(\DateTimeInterface::class, $payment->getPaymentDueDateObject());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('20250301', $payment->getFormattedPaymentDueDate());
        
        // Test with DateTime object
        $dateTime = new \DateTime('2025-03-01');
        $payment->setPaymentDueDate($dateTime);
        $this->assertInstanceOf(\DateTimeInterface::class, $payment->getPaymentDueDateObject());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('20250301', $payment->getFormattedPaymentDueDate());
        
        // Test with timestamp
        $timestamp = strtotime('2025-03-01');
        $payment->setPaymentDueDate($timestamp);
        $this->assertInstanceOf(\DateTimeInterface::class, $payment->getPaymentDueDateObject());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('20250301', $payment->getFormattedPaymentDueDate());
    }
    
    /**
     * Test invalid payment due date format
     */
    public function testInvalidPaymentDueDateFormat(): void
    {
        $payment = new Payment();
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid payment due date format');
        
        $payment->setPaymentDueDate('not-a-date');
    }
    
    /**
     * Test toTabDelimitedString method
     */
    public function testToTabDelimitedString(): void
    {
        $payment = new Payment();
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('USD');
        $payment->setPaymentDueDate('20250301');
        $payment->setVariableSymbol('12345');
        $payment->setConstantSymbol('0308');
        $payment->setSpecificSymbol('54321');
        $payment->setPaymentNote('Test payment');
        
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $payment->addBankAccount($bankAccount);
        
        $tabDelimitedString = $payment->toTabDelimitedString();
        
        $this->assertIsString($tabDelimitedString);
        $this->assertStringContainsString('123.45', $tabDelimitedString);
        $this->assertStringContainsString('USD', $tabDelimitedString);
        $this->assertStringContainsString('20250301', $tabDelimitedString);
        $this->assertStringContainsString('12345', $tabDelimitedString);
        $this->assertStringContainsString('0308', $tabDelimitedString);
        $this->assertStringContainsString('54321', $tabDelimitedString);
        $this->assertStringContainsString('Test payment', $tabDelimitedString);
        $this->assertStringContainsString('SK2483300000002403097934', $tabDelimitedString);
        $this->assertStringContainsString('FIOZSKBAXXX', $tabDelimitedString);
        
        // Verify the format (tab-delimited)
        $parts = explode("\t", $tabDelimitedString);
        $this->assertGreaterThan(10, count($parts)); // Should have many tab-separated parts
        
        // Test with multiple bank accounts
        $payment2 = new Payment();
        $payment2->setAmount(123.45);
        $payment2->setCurrencyCode('USD');
        $bankAccount1 = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $bankAccount2 = new BankAccount('CZ6508000000192000145399', 'GIBACZPX');
        $payment2->addBankAccount($bankAccount1);
        $payment2->addBankAccount($bankAccount2);
        
        $tabDelimitedString2 = $payment2->toTabDelimitedString();
        
        $this->assertIsString($tabDelimitedString2);
        $this->assertStringContainsString('SK2483300000002403097934', $tabDelimitedString2);
        $this->assertStringContainsString('FIOZSKBAXXX', $tabDelimitedString2);
        $this->assertStringContainsString('CZ6508000000192000145399', $tabDelimitedString2);
        $this->assertStringContainsString('GIBACZPX', $tabDelimitedString2);
        
        // Verify the number of bank accounts is included
        $this->assertStringContainsString('2', $tabDelimitedString2);
    }
}