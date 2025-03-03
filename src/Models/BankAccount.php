<?php

namespace PayBySquare\Models;

/**
 * Class BankAccount
 * Represents a bank account in the PAY by square format
 */
class BankAccount
{
    /** @var string IBAN code */
    private string $iban;
    
    /** @var string|null BIC/SWIFT code */
    private ?string $bic = null;
    
    /**
     * BankAccount constructor
     *
     * @param string $iban IBAN code
     * @param string|null $bic BIC/SWIFT code
     */
    public function __construct(string $iban, ?string $bic = null)
    {
        $this->iban = $iban;
        $this->bic = $bic;
    }
    
    /**
     * Set IBAN code
     *
     * @param string $iban IBAN code
     * @return self
     */
    public function setIban(string $iban): self
    {
        $this->iban = $iban;
        return $this;
    }
    
    /**
     * Get IBAN code
     *
     * @return string
     */
    public function getIban(): string
    {
        return $this->iban;
    }
    
    /**
     * Set BIC/SWIFT code
     *
     * @param string $bic BIC/SWIFT code
     * @return self
     */
    public function setBic(string $bic): self
    {
        $this->bic = $bic;
        return $this;
    }
    
    /**
     * Get BIC/SWIFT code
     *
     * @return string|null
     */
    public function getBic(): ?string
    {
        return $this->bic;
    }
    
    /**
     * Convert bank account data to array
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'iban' => $this->iban,
        ];
        
        if ($this->bic !== null) {
            $result['bic'] = $this->bic;
        }
        
        return $result;
    }
}