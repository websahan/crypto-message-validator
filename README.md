# crypto-message-validator
Verify a Bitcoin and Ethereum messages, signed by private key
## API
### `MessageValidator::verifyMessage($address, $message, $signature)`
- `$address`: crypto address (Ethereum or Bitcoin)
- `$message`: signed message
- `$signature`: signature

Return true if signature is valid or false otherwise
## Usage
```php
use SWT\MessageValidator;

$messageValidator = new MessageValidator();

// verify Ethereum message
$result = $messageValidator->verifyMessage(
    '0x4a6747972645fa691a8914135333b002c0445e36',
    'ADINKRA',
    '0x0ef8a5f1211fed21e0fe25be5e2d5e07d403476bc58ebf8fef7a028de806448c2b3eb0967a8006501d44290df15e94981dc44068da86adb1cbe4d789780628351b'
);

// Verify a Bitcoin message
$result = $messageValidator->verifyMessage(
    '1XyVzVwMiUaqqexwvR81CVkPPp1LVYjwM',
    'websahan',
    'IEidgGsL4fB/xlzVqedX39Lw3b2+HHn973OqduwH9WXuQgCJRcRtFl+EV8FagaFuh/Z29z80rwR8DlydrbIIjpg='
);
```

Library supports of Ethereum signatures, Bitcoin P2PKH, P2WPKH-in-P2SH and Bech32 signatures
