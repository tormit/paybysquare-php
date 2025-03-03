<?php

namespace PayBySquare\Tests\Models;

use PHPUnit\Framework\TestCase;
use PayBySquare\Models\BankAccount;

class BankAccountTest extends TestCase
{
    /**
     * Test BankAccount constructor and getters
     */
    public function testConstructorAndGetters(): void
    {
        // Test with both IBAN and BIC
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $this->assertEquals('SK2483300000002403097934', $bankAccount->getIban());
        $this->assertEquals('FIOZSKBAXXX', $bankAccount->getBic());
        
        // Test with only IBAN (BIC is optional)
        $bankAccountNoSwift = new BankAccount('SK2483300000002403097934');
        $this->assertEquals('SK2483300000002403097934', $bankAccountNoSwift->getIban());
        $this->assertNull($bankAccountNoSwift->getBic());
    }
    
    /**
     * Test BankAccount setters
     */
    public function testSetters(): void
    {
        $bankAccount = new BankAccount('SK2483300000002403097934');
        
        // Test fluent interface (setters return $this)
        $this->assertSame($bankAccount, $bankAccount->setIban('CZ6508000000192000145399'));
        $this->assertSame($bankAccount, $bankAccount->setBic('GIBACZPX'));
        
        // Test that values were actually set
        $this->assertEquals('CZ6508000000192000145399', $bankAccount->getIban());
        $this->assertEquals('GIBACZPX', $bankAccount->getBic());
    }
    
    /**
     * Test toArray method
     */
    public function testToArray(): void
    {
        // Test with both IBAN and BIC
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $array = $bankAccount->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('iban', $array);
        $this->assertArrayHasKey('bic', $array);
        $this->assertEquals('SK2483300000002403097934', $array['iban']);
        $this->assertEquals('FIOZSKBAXXX', $array['bic']);
        
        // Test with only IBAN (BIC is optional)
        $bankAccountNoSwift = new BankAccount('SK2483300000002403097934');
        $arrayNoSwift = $bankAccountNoSwift->toArray();
        
        $this->assertIsArray($arrayNoSwift);
        $this->assertArrayHasKey('iban', $arrayNoSwift);
        $this->assertArrayNotHasKey('bic', $arrayNoSwift);
        $this->assertEquals('SK2483300000002403097934', $arrayNoSwift['iban']);
    }
}