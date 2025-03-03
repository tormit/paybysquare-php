<?php

namespace PayBySquare\Tests\Models;

use PHPUnit\Framework\TestCase;
use PayBySquare\Models\Payment;
use PayBySquare\Models\BankAccount;

class PaymentReferenceTest extends TestCase
{
    /**
     * Test setting and getting reference
     */
    public function testSetAndGetReference(): void
    {
        $payment = new Payment();
        
        // Test setting reference
        $this->assertSame($payment, $payment->setReference('REF123'));
        $this->assertEquals('REF123', $payment->getReference());
        
        // Test that symbols are cleared when setting reference
        $this->assertNull($payment->getVariableSymbol());
        $this->assertNull($payment->getConstantSymbol());
        $this->assertNull($payment->getSpecificSymbol());
    }
    
    /**
     * Test that reference is cleared when setting symbols
     */
    public function testReferenceClearedWhenSettingSymbols(): void
    {
        $payment = new Payment();
        $payment->setReference('REF123');
        
        // Test that reference is cleared when setting variable symbol
        $payment->setVariableSymbol('12345');
        $this->assertEquals('12345', $payment->getVariableSymbol());
        $this->assertNull($payment->getReference());
        
        // Reset
        $payment->setReference('REF123');
        
        // Test that reference is cleared when setting constant symbol
        $payment->setConstantSymbol('0308');
        $this->assertEquals('0308', $payment->getConstantSymbol());
        $this->assertNull($payment->getReference());
        
        // Reset
        $payment->setReference('REF123');
        
        // Test that reference is cleared when setting specific symbol
        $payment->setSpecificSymbol('54321');
        $this->assertEquals('54321', $payment->getSpecificSymbol());
        $this->assertNull($payment->getReference());
    }
    
    /**
     * Test that reference is included in toArray output
     */
    public function testReferenceInToArray(): void
    {
        $payment = new Payment();
        $payment->setReference('REF123');
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('USD');
        $payment->addBankAccount(new BankAccount('SK2483300000002403097934'));
        
        $array = $payment->toArray();
        
        $this->assertArrayHasKey('reference', $array);
        $this->assertEquals('REF123', $array['reference']);
        
        // Test that symbols are not included
        $this->assertArrayNotHasKey('variableSymbol', $array);
        $this->assertArrayNotHasKey('constantSymbol', $array);
        $this->assertArrayNotHasKey('specificSymbol', $array);
    }
    
    /**
     * Test that reference is included in toTabDelimitedString output
     */
    public function testReferenceInToTabDelimitedString(): void
    {
        $payment = new Payment();
        $payment->setReference('REF123');
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('USD');
        $payment->addBankAccount(new BankAccount('SK2483300000002403097934'));
        
        $tabDelimitedString = $payment->toTabDelimitedString();
        
        $this->assertIsString($tabDelimitedString);
        $this->assertStringContainsString('REF123', $tabDelimitedString);
        
        // Verify the format (tab-delimited)
        $parts = explode("\t", $tabDelimitedString);
        
        // The reference should be at index 7 (0-based) in the payment data
        // But since the payment data starts at index 2 in the full string, it's at index 9
        $this->assertEquals('REF123', $parts[9]);
        
        // Check that the symbols are empty
        $this->assertEquals('', $parts[6]); // Variable symbol
        $this->assertEquals('', $parts[7]); // Constant symbol
        $this->assertEquals('', $parts[8]); // Specific symbol
    }
    
    /**
     * Test that symbols are included in toArray output when set
     */
    public function testSymbolsInToArray(): void
    {
        $payment = new Payment();
        $payment->setVariableSymbol('12345');
        $payment->setConstantSymbol('0308');
        $payment->setSpecificSymbol('54321');
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('USD');
        $payment->addBankAccount(new BankAccount('SK2483300000002403097934'));
        
        $array = $payment->toArray();
        
        $this->assertArrayHasKey('variableSymbol', $array);
        $this->assertArrayHasKey('constantSymbol', $array);
        $this->assertArrayHasKey('specificSymbol', $array);
        $this->assertEquals('12345', $array['variableSymbol']);
        $this->assertEquals('0308', $array['constantSymbol']);
        $this->assertEquals('54321', $array['specificSymbol']);
        
        // Test that reference is not included
        $this->assertArrayNotHasKey('originatorsReferenceInformation', $array);
    }
    
    /**
     * Test that symbols are included in toTabDelimitedString output when set
     */
    public function testSymbolsInToTabDelimitedString(): void
    {
        $payment = new Payment();
        $payment->setVariableSymbol('12345');
        $payment->setConstantSymbol('0308');
        $payment->setSpecificSymbol('54321');
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('USD');
        $payment->addBankAccount(new BankAccount('SK2483300000002403097934'));
        
        $tabDelimitedString = $payment->toTabDelimitedString();
        
        $this->assertIsString($tabDelimitedString);
        $this->assertStringContainsString('12345', $tabDelimitedString);
        $this->assertStringContainsString('0308', $tabDelimitedString);
        $this->assertStringContainsString('54321', $tabDelimitedString);
        
        // Verify the format (tab-delimited)
        $parts = explode("\t", $tabDelimitedString);
        
        // Check that the symbols are set
        $this->assertEquals('12345', $parts[6]); // Variable symbol
        $this->assertEquals('0308', $parts[7]); // Constant symbol
        $this->assertEquals('54321', $parts[8]); // Specific symbol
        
        // Check that the reference is empty
        $this->assertEquals('', $parts[9]); // Reference
    }
}