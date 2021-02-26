<?php
/**
 * Created by PhpStorm.
 * User: sahan
 * Date: 21.02.21
 * Time: 1:08
 */

namespace SWT;


use Elliptic\Curve\ShortCurve\Point;
use Elliptic\EC;
use kornrunner\Keccak;

class ETHSignature implements ISignature
{
    public $isTrezor = false;

    public function verifySignature($address, $message, $signature)
    {
        if (strlen($signature) === 130) {
            $signature = '0x' . $signature;
        }

        $signatureArray = [
            'r' => substr($signature, 2, 64),
            's' => substr($signature, 66, 64)
        ];

        $recoveryId = $this->recoveryId($signature);

        $messageHash = $this->hashMessage($message);

        $ec = new EC('secp256k1');

        $publicKey = $ec->recoverPubKey($messageHash, $signatureArray, $recoveryId);

        $recoveredAddress = $this->pubKeyToAddress($publicKey);

        return $this->isAddressesMatch($address, $recoveredAddress);
    }

    public function hashMessage($message)
    {
        $messageLenth = strlen($message);

        if ($this->isTrezor) {
            $messageLenth = chr($messageLenth);
        }

        return Keccak::hash("\x19Ethereum Signed Message:\n{$messageLenth}{$message}", 256);
    }

    public function pubKeyToAddress(Point $publicKey)
    {
        return '0x' . substr(Keccak::hash(substr(hex2bin($publicKey->encode('hex')), 1), 256), 24);
    }

    public function recoveryId($signature)
    {
        $recoveryId  = ord(hex2bin(substr($signature, 130, 2))) - 27;

        if ($recoveryId != ($recoveryId & 1)) {
            throw new \Exception('Incorrect recovery id');
        }

        return $recoveryId;
    }

    private function isAddressesMatch($address, $recoveredAddress)
    {
        return strtolower($address) === $recoveredAddress;
    }
}