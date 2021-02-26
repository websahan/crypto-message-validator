<?php
/**
 * https://en.bitcoin.it/wiki/BIP_0137
 */

namespace SWT;


use Elliptic\Curve\ShortCurve\Point;
use Elliptic\EC;
use StephenHill\Base58;

class BTCSignature implements ISignature
{
    const PREFIX_NET_ID_MAP = [
        '1' => "\x00",
        '3' => "\x05",
        'b' => "",
        'm' => "\x6F"
    ];

    protected $netId = '';

    protected $address;

    public $segwitType = false;

    public function verifySignature($address, $message, $signature)
    {
        $signatureBin = base64_decode($signature);
        if (strlen($signatureBin) !== 65) {
            throw new \Exception('Invalid signature size');
        }

        $signatureArray = [
            'r' => bin2hex(substr($signatureBin, 1, 32)),
            's' => bin2hex(substr($signatureBin, 33, 32))
        ];

        $this->address = $address;

        $this->getNetId($address);

        $recoveryId = $this->recoveryId($signatureBin);

        $messageHash = $this->hashMessage($message);

        $ec = new EC('secp256k1');

        $publicKey = $ec->recoverPubKey($messageHash, $signatureArray, $recoveryId);

        $recoveredAddress = $this->pubKeyToAddress($publicKey);

        return $address === $recoveredAddress;
    }

    public function hashMessage($message)
    {
        $messageLength = strlen($message);
        $messageHash = hash(
            'sha256',
            hash(
                'sha256',
                "\x18Bitcoin Signed Message:\n" . chr($messageLength) . $message,
                true
            )
        );
        return $messageHash;
    }

    public function pubKeyToAddress(Point $publicKey)
    {
        $publicKeyBin = hex2bin($publicKey->encode('hex', true));
        $publicKeyHash = $this->netId . ($this->isP2SHType() ? $this->redeemScript($publicKeyBin) : $this->hash160($publicKeyBin));

        if ($this->isBech32Type()) {

            $programChars = array_values(unpack('C*', $publicKeyHash));
            $programBits = \BitWasp\Bech32\convertBits($programChars, count($programChars), 8, 5, true);
            $encodeData = array_merge([0], $programBits);
            return \BitWasp\Bech32\encode('bc', $encodeData);

        } else {

            $base58 = new Base58();
            $checksum = $this->checksum($publicKeyHash);
            return $base58->encode($publicKeyHash . $checksum);
        }
    }

    public function recoveryId($signature)
    {
        $headerData = ord($signature[0]);

        if ($headerData < 27 || $headerData > 42) {
            throw new \Exception('Invalid header value');
        }

        $flagByte = $headerData - ($this->isSegwitType($headerData) ? 35 : 27);

        $recoveryId = ($flagByte & 3);

        return $recoveryId;
    }

    protected function isSegwitType($header)
    {
        return $header > 34;
    }

    public function isBech32Type()
    {
        return preg_match('/^[bc]/', $this->address) === 1;
    }

    public function isP2SHType()
    {
        return preg_match('/^[3]/', $this->address) === 1;
    }

    protected function hash160($publicKeyHash)
    {
        return hash('ripemd160', hash('sha256', $publicKeyHash, true), true);
    }

    protected function redeemScript($data)
    {
        return $this->hash160(hex2bin('0014') . $this->hash160($data));
    }

    protected function checksum($data)
    {
        return substr(hash('sha256', hash('sha256', $data, true), true), 0, 4);
    }

    protected function getNetId($address)
    {
        $this->netId = self::PREFIX_NET_ID_MAP[$address[0]];
    }

}