<?php
/**
 * Created by PhpStorm.
 * User: sahan
 * Date: 21.02.21
 * Time: 1:06
 */

namespace SWT;



use Elliptic\Curve\ShortCurve\Point;

interface ISignature
{

    public function verifySignature($address, $message, $signature);

    public function hashMessage($message);

    public function pubKeyToAddress(Point $publicKey);

    public function recoveryId($signature);
}