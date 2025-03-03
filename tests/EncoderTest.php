<?php

namespace PayBySquare\Tests;

use PHPUnit\Framework\TestCase;
use PayBySquare\Encoder;
use PayBySquare\Models\Payment;
use PayBySquare\Models\BankAccount;
use PayBySquare\Exceptions\EncodingException;

class EncoderTest extends TestCase
{
    /**
     * @var Encoder
     */
    private Encoder $encoder;
    
    /**
     * Set up test environment
     */
    protected function setUp(): void
    {
        $this->encoder = new Encoder();
    }
    
    /**
     * Test that encode method returns a string
     * 
     * @dataProvider paymentProvider
     */
    public function testEncodeReturnsString(Payment $payment): void
    {
        try {
            $result = $this->encoder->encode($payment);
            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        } catch (EncodingException $e) {
            // If the test environment doesn't have the XZ binary, this test will be skipped
            $this->markTestSkipped('XZ binary not available: ' . $e->getMessage());
        }
    }
    
    /**
     * Test that encode method produces consistent results for the same input
     * 
     * @dataProvider paymentProvider
     */
    public function testEncodeProducesConsistentResults(Payment $payment): void
    {
        try {
            $result1 = $this->encoder->encode($payment);
            $result2 = $this->encoder->encode($payment);
            
            $this->assertEquals($result1, $result2, 'Encoding the same payment twice should produce the same result');
        } catch (EncodingException $e) {
            // If the test environment doesn't have the XZ binary, this test will be skipped
            $this->markTestSkipped('XZ binary not available: ' . $e->getMessage());
        }
    }
    
    /**
     * Test that encode method produces the expected output for each test case
     * 
     * @dataProvider paymentWithExpectedOutputProvider
     */
    public function testEncodeProducesExpectedOutput(Payment $payment, string $expectedOutput): void
    {
        try {
            $result = $this->encoder->encode($payment);
            $this->assertEquals($expectedOutput, $result, 'Encoding should produce the expected output');
        } catch (EncodingException $e) {
            // If the test environment doesn't have the XZ binary, this test will be skipped
            $this->markTestSkipped('XZ binary not available: ' . $e->getMessage());
        }
    }
    
    /**
     * Data provider for payment objects
     * 
     * @return array
     */
    public function paymentProvider(): array
    {
        return [
            'Basic Payment (Minimal Attributes)' => [$this->createBasicPayment()],
            'Full Payment (All Attributes)' => [$this->createFullPayment()],
            'Payment with Special Characters' => [$this->createPaymentWithSpecialCharacters()],
        ];
    }
    
    /**
     * Data provider for payment objects with expected outputs
     * 
     * @return array
     */
    public function paymentWithExpectedOutputProvider(): array
    {
        return [
            'Basic Payment (Minimal Attributes)' => [
                $this->createBasicPayment(),
                '00040000BQDCP1NG92PSOB69AVG0V9OQ0UL0RDDH2MTIQ95V7AG003E2E0A245CESUH2IM8L1N538CE5CVQ1E6B924GU9BNMIFVVH7J800'
            ],
            'Full Payment (All Attributes)' => [
                $this->createFullPayment(),
                '000A80008E551G4S6VUUH0GVGI739VRQN4JS1CRNM1P9FFMGP0U04UQN4E5BKGF9NPN8JAKQE1VK0U1B5ACISK3133PSET00B1DK6TQT7HGE8HTMEU7OJUHMPPCSFHSLS7T276LKIIOOJUT4KHNG5IS76TOEKQ9QJK2C3KG4FPLRQ8R27A9C1R44Q81EC3MMSV6QNQ79KBIMG9P57S8G7EH39DVNH0JNULOQ9BVUVKCN800'
            ],
            'Payment with Special Characters' => [
                $this->createPaymentWithSpecialCharacters(),
                '0009C000FU53O16O92PUMF2NPD3DVI2E4SN2ESD72I0GBKP3593TJIVO1B458KRG4MM83ID5MMCP9OF0RAT3FHJLT0MO0DVR8P5ONNL74ND2EOQUJUU2FE1NF2792UEN81PD8326NGCA7VAT2BQQN9G9B8O7FK61GD1UL6E14SMIGFA64VA7BNR75R0TMAD4QHAEDH6307AQGE330HPE6FACS9KE7FUJFSTJTQALJ1N49SJTVUP8VG00'
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