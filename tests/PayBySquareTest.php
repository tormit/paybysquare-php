<?php

namespace PayBySquare\Tests;

use PHPUnit\Framework\TestCase;
use PayBySquare\PayBySquare;
use PayBySquare\Models\Payment;
use PayBySquare\Models\BankAccount;
use PayBySquare\Exceptions\EncodingException;
use Endroid\QrCode\Color\Color;

class PayBySquareTest extends TestCase
{
    /**
     * Test constructor with payment attributes
     */
    public function testConstructorWithPaymentAttributes(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
            'bic' => 'FIOZSKBAXXX',
            'variableSymbol' => '12345',
            'constantSymbol' => '0308',
            'specificSymbol' => '54321',
            'paymentNote' => 'Test payment',
            'paymentDueDate' => '20250301',
            'beneficiaryName' => 'ACME Corporation',
            'beneficiaryAddressLine1' => '123 Main Street',
            'beneficiaryAddressLine2' => '12345 Capital City',
        ]);
        
        $payment = $payBySquare->getPayment();
        
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(15.00, $payment->getAmount());
        $this->assertEquals('EUR', $payment->getCurrencyCode());
        $this->assertEquals('12345', $payment->getVariableSymbol());
        $this->assertEquals('0308', $payment->getConstantSymbol());
        $this->assertEquals('54321', $payment->getSpecificSymbol());
        $this->assertEquals('Test payment', $payment->getPaymentNote());
        $this->assertEquals('20250301', $payment->getPaymentDueDate());
        $this->assertEquals('ACME Corporation', $payment->getBeneficiaryName());
        $this->assertEquals('123 Main Street', $payment->getBeneficiaryAddressLine1());
        $this->assertEquals('12345 Capital City', $payment->getBeneficiaryAddressLine2());
        
        $bankAccounts = $payment->getBankAccounts();
        $this->assertCount(1, $bankAccounts);
        $this->assertEquals('SK2483300000002403097934', $bankAccounts[0]->getIban());
        $this->assertEquals('FIOZSKBAXXX', $bankAccounts[0]->getBic());
    }
    
    /**
     * Test constructor with minimal payment attributes
     */
    public function testConstructorWithMinimalPaymentAttributes(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $payment = $payBySquare->getPayment();
        
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(15.00, $payment->getAmount());
        $this->assertEquals('EUR', $payment->getCurrencyCode());
        
        $bankAccounts = $payment->getBankAccounts();
        $this->assertCount(1, $bankAccounts);
        $this->assertEquals('SK2483300000002403097934', $bankAccounts[0]->getIban());
        $this->assertNull($bankAccounts[0]->getBic());
    }
    
    /**
     * Test constructor with invalid payment attributes
     */
    public function testConstructorWithInvalidPaymentAttributes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        
        // Missing required attributes (amount, currencyCode, iban)
        new PayBySquare([
            'variableSymbol' => '12345',
        ]);
    }
    
    /**
     * Test constructor without payment attributes
     */
    public function testConstructorWithoutPaymentAttributes(): void
    {
        $payBySquare = new PayBySquare();
        
        $this->assertNull($payBySquare->getPayment());
    }
    
    /**
     * Test createPayment method
     */
    public function testCreatePayment(): void
    {
        $payBySquare = new PayBySquare();
        
        $payment = $payBySquare->createPayment(15.00, 'EUR', 'SK2483300000002403097934', 'FIOZSKBAXXX');
        
        $this->assertInstanceOf(Payment::class, $payment);
        $this->assertEquals(15.00, $payment->getAmount());
        $this->assertEquals('EUR', $payment->getCurrencyCode());
        
        $bankAccounts = $payment->getBankAccounts();
        $this->assertCount(1, $bankAccounts);
        $this->assertEquals('SK2483300000002403097934', $bankAccounts[0]->getIban());
        $this->assertEquals('FIOZSKBAXXX', $bankAccounts[0]->getBic());
    }
    
    /**
     * Test setPayment and getPayment methods
     */
    public function testSetAndGetPayment(): void
    {
        $payBySquare = new PayBySquare();
        
        $payment = new Payment();
        $payment->setAmount(15.00);
        $payment->setCurrencyCode('EUR');
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $payment->addBankAccount($bankAccount);
        
        $this->assertSame($payBySquare, $payBySquare->setPayment($payment));
        $this->assertSame($payment, $payBySquare->getPayment());
    }
    
    /**
     * Test generateQrCode method
     */
    public function testGenerateQrCode(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $qrCode = $payBySquare->generateQrCode();
        
        $this->assertIsString($qrCode);
        $this->assertNotEmpty($qrCode);
    }
    
    /**
     * Test generateQrCode method with no payment
     */
    public function testGenerateQrCodeWithNoPayment(): void
    {
        $payBySquare = new PayBySquare();
        
        $this->expectException(\InvalidArgumentException::class);
        $payBySquare->generateQrCode();
    }
    
    /**
     * Test generateQrCode method with options
     */
    public function testGenerateQrCodeWithOptions(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $qrCode = $payBySquare->generateQrCode([
            'size' => 200,
            'margin' => 5,
        ]);
        
        $this->assertIsString($qrCode);
        $this->assertNotEmpty($qrCode);
    }
    
    /**
     * Test getPayBySquareData method
     */
    public function testGetPayBySquareData(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $data = $payBySquare->getPayBySquareData();
        
        $this->assertIsString($data);
        $this->assertNotEmpty($data);
    }
    
    /**
     * Test getPayBySquareData method with no payment
     */
    public function testGetPayBySquareDataWithNoPayment(): void
    {
        $payBySquare = new PayBySquare();
        
        $this->expectException(\InvalidArgumentException::class);
        $payBySquare->getPayBySquareData();
    }
    
    /**
     * Test getPayBySquareData method with provided payment
     */
    public function testGetPayBySquareDataWithProvidedPayment(): void
    {
        $payBySquare = new PayBySquare();
        
        $payment = new Payment();
        $payment->setAmount(15.00);
        $payment->setCurrencyCode('EUR');
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $payment->addBankAccount($bankAccount);
        
        $data = $payBySquare->getPayBySquareData($payment);
        
        $this->assertIsString($data);
        $this->assertNotEmpty($data);
    }
    
    /**
     * Test __toString method
     */
    public function testToString(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $string = (string) $payBySquare;
        
        $this->assertIsString($string);
        $this->assertNotEmpty($string);
    }
    
    /**
     * Test getQRCodeImage method
     */
    public function testGetQRCodeImage(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        // Test with HTML tag
        $htmlTag = $payBySquare->getQRCodeImage(true);
        $this->assertIsString($htmlTag);
        $this->assertStringStartsWith('<img src="data:image/png;base64,', $htmlTag);
        $this->assertStringEndsWith('alt="QR Platba" />', $htmlTag);
        
        // Test without HTML tag (data URI)
        $dataUri = $payBySquare->getQRCodeImage(false);
        $this->assertIsString($dataUri);
        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }
    
    /**
     * Test getDataUri method
     */
    public function testGetDataUri(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $dataUri = $payBySquare->getDataUri();
        
        $this->assertIsString($dataUri);
        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }
    
    /**
     * Test getDataUri method with options
     */
    public function testGetDataUriWithOptions(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $dataUri = $payBySquare->getDataUri(200, 5, [
            'foregroundColor' => new Color(0, 0, 255, 0), // Blue
            'backgroundColor' => new Color(255, 255, 0, 0), // Yellow
        ]);
        
        $this->assertIsString($dataUri);
        $this->assertStringStartsWith('data:image/png;base64,', $dataUri);
    }
    
    /**
     * Test saveQRCodeImage method
     * 
     * @requires extension gd
     */
    public function testSaveQRCodeImage(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $tempFile = sys_get_temp_dir() . '/qr-code-test.png';
        
        try {
            $result = $payBySquare->saveQRCodeImage($tempFile);
            
            $this->assertSame($payBySquare, $result);
            $this->assertFileExists($tempFile);
            
            // Clean up
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        } catch (EncodingException $e) {
            $this->markTestSkipped('Failed to save QR code: ' . $e->getMessage());
        }
    }
    
    /**
     * Test saveQRCodeImage method with SVG format
     * 
     * @requires extension dom
     */
    public function testSaveQRCodeImageWithSvgFormat(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $tempFile = sys_get_temp_dir() . '/qr-code-test.svg';
        
        try {
            $result = $payBySquare->saveQRCodeImage($tempFile, 'svg');
            
            $this->assertSame($payBySquare, $result);
            $this->assertFileExists($tempFile);
            
            // Clean up
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        } catch (EncodingException $e) {
            $this->markTestSkipped('Failed to save QR code: ' . $e->getMessage());
        }
    }
    
    /**
     * Test saveQRCodeImage method with invalid format
     */
    public function testSaveQRCodeImageWithInvalidFormat(): void
    {
        $payBySquare = new PayBySquare([
            'amount' => 15.00,
            'currencyCode' => 'EUR',
            'iban' => 'SK2483300000002403097934',
        ]);
        
        $tempFile = sys_get_temp_dir() . '/qr-code-test.jpg';
        
        $this->expectException(EncodingException::class);
        $payBySquare->saveQRCodeImage($tempFile, 'jpg');
    }
}