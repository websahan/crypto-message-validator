<?php

namespace SWT\Tests;

use PHPUnit\Framework\TestCase;
use SWT\MessageValidator;

class MessageValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp(): void
    {
        $this->validator = new MessageValidator();
    }

    public function testEthereumMessage()
    {

        $result = $this->validator->verifyMessage(
            '0x4a6747972645fa691a8914135333b002c0445e36',
            'ADINKRA',
            '0x0ef8a5f1211fed21e0fe25be5e2d5e07d403476bc58ebf8fef7a028de806448c2b3eb0967a8006501d44290df15e94981dc44068da86adb1cbe4d789780628351b'
        );

        $this->assertTrue($result);
    }

    public function testBTCSegwitMessage()
    {
        $result = $this->validator->verifyMessage(
            '3JvL6Ymt8MVWiCNHC7oWU6nLeHNJKLZGLN',
            'vires is numeris',
            'JF8nHqFr3K2UKYahhX3soVeoW8W1ECNbr0wfck7lzyXjCS5Q16Ek45zyBuy1Fiy9sTPKVgsqqOuPvbycuVSSVl8='
        );

        $this->assertTrue($result);
    }

    public function testBTCBech32Message()
    {
        $result = $this->validator->verifyMessage(
            'bc1qw508d6qejxtdg4y5r3zarvary0c5xw7kv8f3t4',
            'vires is numeris',
            'KF8nHqFr3K2UKYahhX3soVeoW8W1ECNbr0wfck7lzyXjCS5Q16Ek45zyBuy1Fiy9sTPKVgsqqOuPvbycuVSSVl8='
        );

        $this->assertTrue($result);
    }

    public function testBTCLegacyMessage()
    {
        $result = $this->validator->verifyMessage(
            '1XyVzVwMiUaqqexwvR81CVkPPp1LVYjwM',
            'websahan',
            'IEidgGsL4fB/xlzVqedX39Lw3b2+HHn973OqduwH9WXuQgCJRcRtFl+EV8FagaFuh/Z29z80rwR8DlydrbIIjpg='
        );

        $this->assertTrue($result);
    }

    public function testBTCTrezorMessage()
    {
        $result = $this->validator->verifyMessage(
            '17DB2Q3oZVkQAffkpFvF4cwsXggu39iKdQ',
            'aaa',
            'IHQ7FDJy6zjwMImIsFcHGdhVxAH7ozoEoelN2EfgKZZ0JVAbvnGN/w8zxiMivqkO8ijw8fXeCMDt0K2OW7q2GF0='
        );

        $this->assertTrue($result);
    }
}