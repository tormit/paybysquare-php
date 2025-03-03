<?php

namespace PayBySquare\Tests;

use PHPUnit\Framework\TestCase;
use PayBySquare\Encoder;
use PayBySquare\Models\Payment;
use PayBySquare\Models\BankAccount;
use PayBySquare\Exceptions\EncodingException;
use ReflectionClass;
use ReflectionMethod;

class EncoderPrivateMethodsTest extends TestCase
{
    /**
     * @var Encoder
     */
    private Encoder $encoder;
    
    /**
     * @var ReflectionClass
     */
    private ReflectionClass $reflector;
    
    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        $this->encoder = new Encoder();
        $this->reflector = new ReflectionClass(Encoder::class);
    }
    
    /**
     * Test calculateCrc32bHash method
     * 
     * @dataProvider hashDataProvider
     */
    public function testCalculateCrc32bHash(string $input, string $expectedOutput): void
    {
        $method = $this->reflector->getMethod('calculateCrc32bHash');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->encoder, $input);
        $this->assertEquals($expectedOutput, $result);
    }
    
    /**
     * Test compressWithLzma method
     * 
     * @dataProvider compressDataProvider
     */
    public function testCompressWithLzma(string $input, string $expectedOutput): void
    {
        $method = $this->reflector->getMethod('compressWithLzma');
        $method->setAccessible(true);
        
        try {
            $result = $method->invoke($this->encoder, $input);
            $this->assertEquals($expectedOutput, $result);
        } catch (EncodingException $e) {
            // If the test environment doesn't have the XZ binary, this test will be skipped
            $this->markTestSkipped('XZ binary not available: ' . $e->getMessage());
        }
    }
    
    /**
     * Test convertToBase32 method
     * 
     * @dataProvider base32DataProvider
     */
    public function testConvertToBase32(string $input, int $input2, string $expectedOutput): void
    {
        $method = $this->reflector->getMethod('convertToBase32');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->encoder, $input, $input2);
        $this->assertEquals($expectedOutput, $result);
    }
    
    /**
     * Data provider for calculateCrc32bHash method
     * 
     * @return array
     */
    public function hashDataProvider(): array
    {
        // Test case 1: Basic Payment
        $input1 = $this->createBasicPayment()->toTabDelimitedString();
        $expectedHash1 = 'bd6b846f';
        
        // Test case 2: Full Payment
        $input2 = $this->createFullPayment()->toTabDelimitedString();
        $expectedHash2 = '8729a609';
        
        // Test case 3: Payment with Special Characters
        $input3 = $this->createPaymentWithSpecialCharacters()->toTabDelimitedString();
        $expectedHash3 = 'ff290050';
        
        return [
            'Basic Payment' => [$input1, hex2bin($expectedHash1)],
            'Full Payment' => [$input2, hex2bin($expectedHash2)],
            'Payment with Special Characters' => [$input3, hex2bin($expectedHash3)],
        ];
    }
    
    /**
     * Data provider for compressWithLzma method
     * 
     * @return array
     */
    public function compressDataProvider(): array
    {
        // Test case 1: Basic Payment
        $input1 = hex2bin('bd6b846f') . $this->createBasicPayment()->toTabDelimitedString();
        $expectedCompressed1 = '005e9acc86f048b3cc2cc957e00fa71a07aa0db5b115bb2d24bf3aa0000dc2701422158ee7a22959150dca3431c567f41719691121e4aef693fff89e6800';
        
        // Test case 2: Full Payment
        $input2 = hex2bin('8729a609') . $this->createFullPayment()->toTabDelimitedString();
        $expectedCompressed2 = '00438a50c09c37fde8821f848e34ff7ab927c0b377b07297bed0c83c027b57238aba41e9be6e89aa9a707f40782b2a992e506118f3c77400585b43775d3c60e447b6778f89fa36ce59c7c795e1fa239ab494b189fba4a46f02cb873770ea693a9d04c1d2047e6bbd23623a92c0ec84d202e60ed6e7cdabe8e9a2e56827253f1103ba234b7f788277f571a4affefd197400';
        
        // Test case 3: Payment with Special Characters
        $input3 = hex2bin('ff290050') . $this->createPaymentWithSpecialCharacters()->toTabDelimitedString();
        $expectedCompressed3 = '007f8a3c04d848b3eb3c57cb46dfc84e272e2771a7148105d3232a47d9cbf80ac854537025ac81c9a5b59994e1e0daba37c675e82d8037fb464b8bdea725da27635e9fbc27b837788e9179d74072d40c46bc18a3fd5d12f5aba6095a3077d0c18343ea99c1272d283d4627d475df672ec1db29a4d454e6c4c301d5a838630472e33d4ce268e3bfd37f3b3ee955986e44f27dffb28fc000';
        
        return [
            'Basic Payment' => [$input1, hex2bin($expectedCompressed1)],
            'Full Payment' => [$input2, hex2bin($expectedCompressed2)],
            'Payment with Special Characters' => [$input3, hex2bin($expectedCompressed3)],
        ];
    }
    
    /**
     * Data provider for convertToBase32 method
     * 
     * @return array
     */
    public function base32DataProvider(): array
    {
        // Test case 1: Basic Payment
        $input1 = hex2bin('005e9acc86f048b3cc2cc957e00fa71a07aa0db5b115bb2d24bf3aa0000dc2701422158ee7a22959150dca3431c567f41719691121e4aef693fff89e6800');
        $input1_2 = 64;
        $expectedBase32_1 = '00040000BQDCP1NG92PSOB69AVG0V9OQ0UL0RDDH2MTIQ95V7AG003E2E0A245CESUH2IM8L1N538CE5CVQ1E6B924GU9BNMIFVVH7J800';
        
        // Test case 2: Full Payment
        $input2 = hex2bin('00438a50c09c37fde8821f848e34ff7ab927c0b377b07297bed0c83c027b57238aba41e9be6e89aa9a707f40782b2a992e506118f3c77400585b43775d3c60e447b6778f89fa36ce59c7c795e1fa239ab494b189fba4a46f02cb873770ea693a9d04c1d2047e6bbd23623a92c0ec84d202e60ed6e7cdabe8e9a2e56827253f1103ba234b7f788277f571a4affefd197400');
        $input2_1 = 164;
        $expectedBase32_2 = '000A80008E551G4S6VUUH0GVGI739VRQN4JS1CRNM1P9FFMGP0U04UQN4E5BKGF9NPN8JAKQE1VK0U1B5ACISK3133PSET00B1DK6TQT7HGE8HTMEU7OJUHMPPCSFHSLS7T276LKIIOOJUT4KHNG5IS76TOEKQ9QJK2C3KG4FPLRQ8R27A9C1R44Q81EC3MMSV6QNQ79KBIMG9P57S8G7EH39DVNH0JNULOQ9BVUVKCN800';
        
        // Test case 3: Payment with Special Characters
        $input3 = hex2bin('007f8a3c04d848b3eb3c57cb46dfc84e272e2771a7148105d3232a47d9cbf80ac854537025ac81c9a5b59994e1e0daba37c675e82d8037fb464b8bdea725da27635e9fbc27b837788e9179d74072d40c46bc18a3fd5d12f5aba6095a3077d0c18343ea99c1272d283d4627d475df672ec1db29a4d454e6c4c301d5a838630472e33d4ce268e3bfd37f3b3ee955986e44f27dffb28fc000');
        $input3_1 = 150;
        $expectedBase32_3 = '0009C000FU53O16O92PUMF2NPD3DVI2E4SN2ESD72I0GBKP3593TJIVO1B458KRG4MM83ID5MMCP9OF0RAT3FHJLT0MO0DVR8P5ONNL74ND2EOQUJUU2FE1NF2792UEN81PD8326NGCA7VAT2BQQN9G9B8O7FK61GD1UL6E14SMIGFA64VA7BNR75R0TMAD4QHAEDH6307AQGE330HPE6FACS9KE7FUJFSTJTQALJ1N49SJTVUP8VG00';
        
        return [
            'Basic Payment' => [$input1, $input1_2, $expectedBase32_1],
            'Full Payment' => [$input2, $input2_1, $expectedBase32_2],
            'Payment with Special Characters' => [$input3, $input3_1, $expectedBase32_3],
        ];
    }
    
    /**
     * Test the full encoding process with known inputs and outputs
     * 
     * @dataProvider fullEncodingDataProvider
     */
    public function testFullEncodingProcess(Payment $payment, string $expectedTabDelimited, string $expectedWithHash, string $expectedCompressed, string $expectedBase32): void
    {
        // Get the tab-delimited string
        $tabDelimitedString = $payment->toTabDelimitedString();
        $this->assertEquals($expectedTabDelimited, $tabDelimitedString);
        
        // Test calculateCrc32bHash
        $addCrc32bHashhMethod = $this->reflector->getMethod('addCrc32bHash');
        $addCrc32bHashhMethod->setAccessible(true);
        $dataWithHash = $addCrc32bHashhMethod->invoke($this->encoder, $tabDelimitedString);
        $this->assertEquals($expectedWithHash, $dataWithHash);
        
        try {
            // Test compressWithLzma
            $compressWithLzmaMethod = $this->reflector->getMethod('compressWithLzma');
            $compressWithLzmaMethod->setAccessible(true);
            $compressed = $compressWithLzmaMethod->invoke($this->encoder, $dataWithHash);
            $this->assertEquals($expectedCompressed, $compressed);
            
            // Test convertToBase32
            $convertToBase32Method = $this->reflector->getMethod('convertToBase32');
            $convertToBase32Method->setAccessible(true);
            $base32 = $convertToBase32Method->invoke($this->encoder, $compressed, strlen($dataWithHash));
            $this->assertEquals($expectedBase32, $base32);
            
            // Test the full encode method
            $result = $this->encoder->encode($payment);
            $this->assertEquals($expectedBase32, $result);
        } catch (EncodingException $e) {
            // If the test environment doesn't have the XZ binary, this test will be skipped
            $this->markTestSkipped('XZ binary not available: ' . $e->getMessage());
        }
    }
    
    /**
     * Data provider for full encoding process
     * 
     * @return array
     */
    public function fullEncodingDataProvider(): array
    {
        // Test case 1: Basic Payment
        $payment1 = $this->createBasicPayment();
        $tabDelimited1 = $payment1->toTabDelimitedString();
        $withHash1 = hex2bin('bd6b846f') . $tabDelimited1;
        $compressed1 = hex2bin('005e9acc86f048b3cc2cc957e00fa71a07aa0db5b115bb2d24bf3aa0000dc2701422158ee7a22959150dca3431c567f41719691121e4aef693fff89e6800');
        $base32_1 = '00040000BQDCP1NG92PSOB69AVG0V9OQ0UL0RDDH2MTIQ95V7AG003E2E0A245CESUH2IM8L1N538CE5CVQ1E6B924GU9BNMIFVVH7J800';
        
        // Test case 2: Full Payment
        $payment2 = $this->createFullPayment();
        $tabDelimited2 = $payment2->toTabDelimitedString();
        $withHash2 = hex2bin('8729a609') . $tabDelimited2;
        $compressed2 = hex2bin('00438a50c09c37fde8821f848e34ff7ab927c0b377b07297bed0c83c027b57238aba41e9be6e89aa9a707f40782b2a992e506118f3c77400585b43775d3c60e447b6778f89fa36ce59c7c795e1fa239ab494b189fba4a46f02cb873770ea693a9d04c1d2047e6bbd23623a92c0ec84d202e60ed6e7cdabe8e9a2e56827253f1103ba234b7f788277f571a4affefd197400');
        $base32_2 = '000A80008E551G4S6VUUH0GVGI739VRQN4JS1CRNM1P9FFMGP0U04UQN4E5BKGF9NPN8JAKQE1VK0U1B5ACISK3133PSET00B1DK6TQT7HGE8HTMEU7OJUHMPPCSFHSLS7T276LKIIOOJUT4KHNG5IS76TOEKQ9QJK2C3KG4FPLRQ8R27A9C1R44Q81EC3MMSV6QNQ79KBIMG9P57S8G7EH39DVNH0JNULOQ9BVUVKCN800';
        
        // Test case 3: Payment with Special Characters
        $payment3 = $this->createPaymentWithSpecialCharacters();
        $tabDelimited3 = $payment3->toTabDelimitedString();
        $withHash3 = hex2bin('ff290050') . $tabDelimited3;
        $compressed3 = hex2bin('007f8a3c04d848b3eb3c57cb46dfc84e272e2771a7148105d3232a47d9cbf80ac854537025ac81c9a5b59994e1e0daba37c675e82d8037fb464b8bdea725da27635e9fbc27b837788e9179d74072d40c46bc18a3fd5d12f5aba6095a3077d0c18343ea99c1272d283d4627d475df672ec1db29a4d454e6c4c301d5a838630472e33d4ce268e3bfd37f3b3ee955986e44f27dffb28fc000');
        $base32_3 = '0009C000FU53O16O92PUMF2NPD3DVI2E4SN2ESD72I0GBKP3593TJIVO1B458KRG4MM83ID5MMCP9OF0RAT3FHJLT0MO0DVR8P5ONNL74ND2EOQUJUU2FE1NF2792UEN81PD8326NGCA7VAT2BQQN9G9B8O7FK61GD1UL6E14SMIGFA64VA7BNR75R0TMAD4QHAEDH6307AQGE330HPE6FACS9KE7FUJFSTJTQALJ1N49SJTVUP8VG00';
        
        return [
            'Basic Payment' => [
                $payment1,
                $tabDelimited1,
                $withHash1,
                $compressed1,
                $base32_1
            ],
            'Full Payment' => [
                $payment2,
                $tabDelimited2,
                $withHash2,
                $compressed2,
                $base32_2
            ],
            'Payment with Special Characters' => [
                $payment3,
                $tabDelimited3,
                $withHash3,
                $compressed3,
                $base32_3
            ],
        ];
    }
    
    /**
     * Create a basic payment with minimal attributes
     * 
     * @return Payment
     */
    private function createBasicPayment(): Payment
    {
        $payment = new Payment();
        $payment->setAmount(15.00);
        $payment->setCurrencyCode('EUR');
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $payment->addBankAccount($bankAccount);
        
        return $payment;
    }
    
    /**
     * Create a full payment with all attributes
     * 
     * @return Payment
     */
    private function createFullPayment(): Payment
    {
        $payment = new Payment();
        $payment->setAmount(123.45);
        $payment->setCurrencyCode('EUR');
        $payment->setPaymentDueDate('20250301');
        $payment->setVariableSymbol('12345');
        $payment->setConstantSymbol('0308');
        $payment->setSpecificSymbol('54321');
        $payment->setPaymentNote('Invoice payment #12345');
        $payment->setBeneficiaryName('ACME Corporation');
        $payment->setBeneficiaryAddressLine1('123 Main Street');
        $payment->setBeneficiaryAddressLine2('12345 Capital City');
        $bankAccount = new BankAccount('SK2483300000002403097934', 'FIOZSKBAXXX');
        $payment->addBankAccount($bankAccount);
        
        return $payment;
    }
    
    /**
     * Create a payment with special characters
     * 
     * @return Payment
     */
    private function createPaymentWithSpecialCharacters(): Payment
    {
        $payment = new Payment();
        $payment->setAmount(99.99);
        $payment->setCurrencyCode('EUR');
        $payment->setPaymentNote('Special characters: áéíóúýčďěňřšťžů');
        $payment->setBeneficiaryName('Jörg Müller');
        $payment->setBeneficiaryAddressLine1('Straße des 17. Juni');
        $bankAccount = new BankAccount('DE89370400440532013000', 'COBADEFFXXX');
        $payment->addBankAccount($bankAccount);
        
        return $payment;
    }
}