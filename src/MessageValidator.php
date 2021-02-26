<?php

namespace SWT;


class MessageValidator
{
    public $isTrezor = false;

    protected $isBitcoin = false;

    protected $isEthereum = false;

    protected $signature;


    /**
     * Verify a signed message
     * @param string $address
     * @param string $message
     * @param string $signature
     * @return bool
     */
    public function verifyMessage($address, $message, $signature)
    {
        $this->detectAddressType($address);

        return $this->signature->verifySignature($address, $message, $signature);

    }

    private function detectAddressType($address)
    {
        if (preg_match('/^[0x]/', $address) === 1) {
            $this->isEthereum = true;
            $this->signature = new ETHSignature();
        } elseif (preg_match('/^[13b]/', $address)) {
            $this->isBitcoin = true;
            $this->signature = new BTCSignature();
        } else {
            throw new \Exception('Unrecognized address type');
        }
    }
}