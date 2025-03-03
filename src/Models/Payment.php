<?php

namespace PayBySquare\Models;

/**
 * Class Payment
 * Represents a payment in the PAY by square format
 */
class Payment
{
    /** @var string[] Payment options */
    private array $paymentOptions = [
        'paymentorder' => 1,
        'standingorder' => 2,
        'directdebit' => 4
    ];

    /**  @var string Payment option */
    private string $paymentOption = 'paymentorder';
    
    /** @var float|null Payment amount */
    private ?float $amount = null;
    
    /** @var string Payment currency code (ISO 4217) */
    private string $currencyCode = 'EUR';
    
    /** @var \DateTimeInterface|null Payment due date */
    private ?\DateTimeInterface $paymentDueDate = null;
    
    /** @var string|null Variable symbol */
    private ?string $variableSymbol = null;
    
    /** @var string|null Constant symbol */
    private ?string $constantSymbol = null;
    
    /** @var string|null Specific symbol */
    private ?string $specificSymbol = null;
    
    /** @var string|null Payment reference */
    private ?string $reference = null;
    
    /** @var string|null Payment note */
    private ?string $paymentNote = null;
    
    /** @var BankAccount[] Bank accounts */
    private array $bankAccounts = [];
    
    /** @var string|null Beneficiary name */
    private ?string $beneficiaryName = null;
    
    /** @var string|null Beneficiary address line 1 */
    private ?string $beneficiaryAddressLine1 = null;
    
    /** @var string|null Beneficiary address line 2 */
    private ?string $beneficiaryAddressLine2 = null;
    
    /**
     * Set payment option
     *
     * @param string $paymentOption Payment option (paymentorder, standingorder, directdebit)
     * @return self
     * @throws \InvalidArgumentException If payment option is invalid
     */
    public function setPaymentOption(string $paymentOption): self
    {
        if (!array_key_exists($paymentOption, $this->paymentOptions)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid payment option "%s". Available options: %s', 
                $paymentOption, 
                implode(', ', array_keys($this->paymentOptions)))
            );
        }
        
        $this->paymentOption = $paymentOption;
        return $this;
    }
    
    /**
     * Get current payment option
     *
     * @return string
     */
    public function getPaymentOption(): string
    {
        return $this->paymentOption;
    }
    
    
    /**
     * Set payment amount
     *
     * @param float $amount Payment amount
     * @return self
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }
    
    /**
     * Get payment amount
     *
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }
    
    /**
     * Set currency code
     *
     * @param string $currencyCode Currency code (ISO 4217)
     * @return self
     */
    public function setCurrencyCode(string $currencyCode): self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }
    
    /**
     * Get currency code
     *
     * @return string
     */
    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
    
    /**
     * Set payment due date
     *
     * @param string|int|\DateTimeInterface $paymentDueDate Payment due date in any recognizable format
     * @return self
     * @throws \InvalidArgumentException If the date format cannot be parsed
     */
    public function setPaymentDueDate($paymentDueDate): self
    {
        try {
            // If already in DateTime format, use it directly
            if ($paymentDueDate instanceof \DateTimeInterface) {
                $this->paymentDueDate = $paymentDueDate;
            } elseif (is_int($paymentDueDate)) {
                // Treat as timestamp
                $dateTime = new \DateTime();
                $dateTime->setTimestamp($paymentDueDate);
                $this->paymentDueDate = $dateTime;
            } elseif (is_string($paymentDueDate)) {
                // If already in YYYYMMDD format, convert to DateTime
                if (preg_match('/^\d{8}$/', $paymentDueDate)) {
                    $this->paymentDueDate = \DateTime::createFromFormat('Ymd', $paymentDueDate);
                } else {
                    // Try to parse the string
                    $this->paymentDueDate = new \DateTime($paymentDueDate);
                }
            } else {
                throw new \InvalidArgumentException('Payment due date must be a string, integer timestamp, or DateTime object');
            }
        } catch (\Exception $e) {
            throw new \InvalidArgumentException(
                sprintf('Invalid payment due date format: %s. Use any recognizable date format.', $paymentDueDate)
            );
        }
        
        return $this;
    }
    
    /**
     * Get payment due date
     *
     * @return string|null
     */
    public function getPaymentDueDate(): ?string
    {
        return $this->paymentDueDate ? $this->paymentDueDate->format('Ymd') : null;
    }
    
    /**
     * Get payment due date as DateTime object
     *
     * @return \DateTimeInterface|null
     */
    public function getPaymentDueDateObject(): ?\DateTimeInterface
    {
        return $this->paymentDueDate;
    }
    
    /**
     * Get payment due date formatted as YYYYMMDD
     *
     * @return string|null
     */
    public function getFormattedPaymentDueDate(): ?string
    {
        return $this->getPaymentDueDate();
    }
    
    /**
     * Set variable symbol
     *
     * @param string $variableSymbol Variable symbol
     * @return self
     */
    public function setVariableSymbol(string $variableSymbol): self
    {
        $this->variableSymbol = $variableSymbol;
        // Clear reference when setting variable symbol
        $this->reference = null;
        return $this;
    }
    
    /**
     * Get variable symbol
     *
     * @return string|null
     */
    public function getVariableSymbol(): ?string
    {
        return $this->variableSymbol;
    }
    
    /**
     * Set constant symbol
     *
     * @param string $constantSymbol Constant symbol
     * @return self
     */
    public function setConstantSymbol(string $constantSymbol): self
    {
        $this->constantSymbol = $constantSymbol;
        // Clear reference when setting constant symbol
        $this->reference = null;
        return $this;
    }
    
    /**
     * Get constant symbol
     *
     * @return string|null
     */
    public function getConstantSymbol(): ?string
    {
        return $this->constantSymbol;
    }
    
    /**
     * Set specific symbol
     *
     * @param string $specificSymbol Specific symbol
     * @return self
     */
    public function setSpecificSymbol(string $specificSymbol): self
    {
        $this->specificSymbol = $specificSymbol;
        // Clear reference when setting specific symbol
        $this->reference = null;
        return $this;
    }
    
    /**
     * Get specific symbol
     *
     * @return string|null
     */
    public function getSpecificSymbol(): ?string
    {
        return $this->specificSymbol;
    }
    
    /**
     * Set payment reference
     *
     * @param string $reference Payment reference
     * @return self
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        // Clear symbols when setting reference
        $this->variableSymbol = null;
        $this->constantSymbol = null;
        $this->specificSymbol = null;
        return $this;
    }
    
    /**
     * Get payment reference
     *
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }
    
    /**
     * Set payment note
     *
     * @param string $paymentNote Payment note
     * @return self
     */
    public function setPaymentNote(string $paymentNote): self
    {
        $this->paymentNote = $paymentNote;
        return $this;
    }
    
    /**
     * Get payment note
     *
     * @return string|null
     */
    public function getPaymentNote(): ?string
    {
        return $this->paymentNote;
    }
    
    /**
     * Add bank account
     *
     * @param BankAccount $bankAccount Bank account
     * @return self
     */
    public function addBankAccount(BankAccount $bankAccount): self
    {
        $this->bankAccounts[] = $bankAccount;
        return $this;
    }
    
    /**
     * Set bank accounts
     *
     * @param BankAccount[] $bankAccounts Bank accounts
     * @return self
     */
    public function setBankAccounts(array $bankAccounts): self
    {
        $this->bankAccounts = $bankAccounts;
        return $this;
    }
    
    /**
     * Get bank accounts
     *
     * @return BankAccount[]
     */
    public function getBankAccounts(): array
    {
        return $this->bankAccounts;
    }
    
    /**
     * Set beneficiary name
     *
     * @param string $beneficiaryName Beneficiary name
     * @return self
     */
    public function setBeneficiaryName(string $beneficiaryName): self
    {
        $this->beneficiaryName = $beneficiaryName;
        return $this;
    }
    
    /**
     * Get beneficiary name
     *
     * @return string|null
     */
    public function getBeneficiaryName(): ?string
    {
        return $this->beneficiaryName;
    }
    
    /**
     * Set beneficiary address line 1
     *
     * @param string $beneficiaryAddressLine1 Beneficiary address line 1
     * @return self
     */
    public function setBeneficiaryAddressLine1(string $beneficiaryAddressLine1): self
    {
        $this->beneficiaryAddressLine1 = $beneficiaryAddressLine1;
        return $this;
    }
    
    /**
     * Get beneficiary address line 1
     *
     * @return string|null
     */
    public function getBeneficiaryAddressLine1(): ?string
    {
        return $this->beneficiaryAddressLine1;
    }
    
    /**
     * Set beneficiary address line 2
     *
     * @param string $beneficiaryAddressLine2 Beneficiary address line 2
     * @return self
     */
    public function setBeneficiaryAddressLine2(string $beneficiaryAddressLine2): self
    {
        $this->beneficiaryAddressLine2 = $beneficiaryAddressLine2;
        return $this;
    }
    
    /**
     * Get beneficiary address line 2
     *
     * @return string|null
     */
    public function getBeneficiaryAddressLine2(): ?string
    {
        return $this->beneficiaryAddressLine2;
    }
    
    /**
     * Convert payment data to array for encoding
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'paymentOption' => $this->paymentOption,
            'amount' => $this->amount,
            'currencyCode' => $this->currencyCode,
        ];
        
        if ($this->paymentDueDate !== null) {
            $result['paymentDueDate'] = $this->getFormattedPaymentDueDate();
        }
        
        if ($this->variableSymbol !== null) {
            $result['variableSymbol'] = $this->variableSymbol;
        }
        
        if ($this->constantSymbol !== null) {
            $result['constantSymbol'] = $this->constantSymbol;
        }
        
        if ($this->specificSymbol !== null) {
            $result['specificSymbol'] = $this->specificSymbol;
        }
        
        if ($this->reference !== null) {
            $result['reference'] = $this->reference;
        }
        
        if ($this->paymentNote !== null) {
            $result['paymentNote'] = $this->paymentNote;
        }
        
        $bankAccounts = [];
        foreach ($this->bankAccounts as $bankAccount) {
            $bankAccounts[] = $bankAccount->toArray();
        }
        $result['bankAccounts'] = $bankAccounts;
        
        if ($this->beneficiaryName !== null) {
            $result['beneficiaryName'] = $this->beneficiaryName;
        }
        
        if ($this->beneficiaryAddressLine1 !== null) {
            $result['beneficiaryAddressLine1'] = $this->beneficiaryAddressLine1;
        }
        
        if ($this->beneficiaryAddressLine2 !== null) {
            $result['beneficiaryAddressLine2'] = $this->beneficiaryAddressLine2;
        }
        
        return $result;
    }
    
    /**
     * Convert payment data to tab-delimited string for PAY by square encoding
     *
     * @return string
     */
    public function toTabDelimitedString(): string
    {
        $paymentData = [
            $this->paymentOptions[$this->paymentOption],
            $this->amount,
            $this->currencyCode,
            $this->getPaymentDueDate() ?? '',
            $this->variableSymbol ?? '',
            $this->constantSymbol ?? '',
            $this->specificSymbol ?? '',
            $this->reference ?? '',
            $this->paymentNote ?? '',
            count($this->bankAccounts),
        ];
        
        // Add bank account data
        foreach ($this->bankAccounts as $bankAccount) {
            $paymentData[] = $bankAccount->getIban();
            $paymentData[] = $bankAccount->getBic() ?? '';
        }
        
        // Add zeros for StandingOrderExt and DirectDebitExt
        $paymentData[] = '0';
        $paymentData[] = '0';

        // add beneficary details
        if ($this->beneficiaryName !== null) {
            $paymentData[] = $this->beneficiaryName;
        }

        if ($this->beneficiaryAddressLine1 !== null) {
            $paymentData[] = $this->beneficiaryAddressLine1;
        }

        if ($this->beneficiaryAddressLine2 !== null) {
            $paymentData[] = $this->beneficiaryAddressLine2;
        }
        
        $result = implode("\t", [
            0 => '',
            1 => '1',
            2 => implode("\t", $paymentData)
        ]);
        
        return $result;
    }
}