<?php

namespace Navigator\Encryption;

use Navigator\Collections\Arr;
use Navigator\Encryption\Exceptions\DecryptException;
use Navigator\Encryption\Exceptions\EncryptException;
use RuntimeException;

class Encrypter
{
    private static array $supportedCiphers = [
        'aes-128-cbc' => ['size' => 16, 'aead' => false],
        'aes-256-cbc' => ['size' => 32, 'aead' => false],
        'aes-128-gcm' => ['size' => 16, 'aead' => true],
        'aes-256-gcm' => ['size' => 32, 'aead' => true],
    ];

    public function __construct(public readonly string $key, public readonly string $cipher = 'aes-256-cbc')
    {
        if (!static::supported($key, $cipher)) {
            $ciphers = implode(', ', Arr::keys(self::$supportedCiphers));

            throw new RuntimeException("Unsupported cipher or incorrect key length. Supported ciphers are: {$ciphers}.");
        }
    }

    public static function supported(string $key, string $cipher): bool
    {
        if (!isset(self::$supportedCiphers[strtolower($cipher)])) {
            return false;
        }

        return mb_strlen($key, '8bit') === self::$supportedCiphers[strtolower($cipher)]['size'];
    }

    public static function generateKey(string $cipher = 'aes-256-cbc'): string
    {
        return random_bytes(self::$supportedCiphers[strtolower($cipher)]['size'] ?? 32);
    }

    public function encrypt(mixed $value, bool $serialize = true): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(strtolower($this->cipher)));

        $value = openssl_encrypt(
            $serialize ? serialize($value) : $value,
            strtolower($this->cipher),
            $this->key,
            0,
            $iv,
            $tag
        );

        if ($value === false) {
            throw new EncryptException('Could not encrypt the data.');
        }

        $iv = base64_encode($iv);
        $tag = base64_encode($tag ?? '');

        $mac = self::$supportedCiphers[strtolower($this->cipher)]['aead']
            ? '' // For AEAD-algorithms, the tag / MAC is returned by openssl_encrypt...
            : $this->hash($iv, $value, $this->key);

        $json = json_encode(compact('iv', 'value', 'mac', 'tag'), JSON_UNESCAPED_SLASHES);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    public function encryptString(string $value): string
    {
        return $this->encrypt($value, false);
    }

    public function decrypt(string $payload, bool $unserialize = true): mixed
    {
        $payload = $this->getJsonPayload($payload);

        $iv = base64_decode($payload['iv']);

        $this->ensureTagIsValid(
            $tag = empty($payload['tag']) ? null : base64_decode($payload['tag'])
        );

        $foundValidMac = false;

        // Here we will decrypt the value. If we are able to successfully decrypt it
        // we will then unserialize it and return it out to the caller. If we are
        // unable to decrypt this value we will throw out an exception message.
        foreach ([$this->key] as $key) {
            if (
                $this->shouldValidateMac() &&
                !($foundValidMac = $foundValidMac || $this->validMacForKey($payload, $key))
            ) {
                continue;
            }

            $decrypted = openssl_decrypt($payload['value'], strtolower($this->cipher), $key, 0, $iv, $tag ?? '');

            if ($decrypted !== false) {
                break;
            }
        }

        if ($this->shouldValidateMac() && !$foundValidMac) {
            throw new DecryptException('The MAC is invalid.');
        }

        if (($decrypted ?? false) === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    public function decryptString(string $payload): string
    {
        return $this->decrypt($payload, false);
    }

    protected function hash(string $iv, mixed $value, string $key): string
    {
        return hash_hmac('sha256', $iv . $value, $key);
    }

    protected function getJsonPayload(string $payload): array
    {
        if (!is_string($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        $payload = json_decode(base64_decode($payload), true);

        // If the payload is not valid JSON or does not have the proper keys set we will
        // assume it is invalid and bail out of the routine since we will not be able
        // to decrypt the given value. We'll also check the MAC for this encryption.
        if (!$this->validPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        return $payload;
    }

    protected function validPayload(mixed $payload): bool
    {
        if (!is_array($payload)) {
            return false;
        }

        foreach (['iv', 'value', 'mac'] as $item) {
            if (!isset($payload[$item]) || !is_string($payload[$item])) {
                return false;
            }
        }

        if (isset($payload['tag']) && !is_string($payload['tag'])) {
            return false;
        }

        return strlen(base64_decode($payload['iv'], true)) === openssl_cipher_iv_length(strtolower($this->cipher));
    }

    protected function validMac(array $payload): bool
    {
        return $this->validMacForKey($payload, $this->key);
    }

    protected function validMacForKey(array $payload, string $key): bool
    {
        return hash_equals(
            $this->hash($payload['iv'], $payload['value'], $key),
            $payload['mac']
        );
    }

    protected function ensureTagIsValid(?string $tag): void
    {
        if (self::$supportedCiphers[strtolower($this->cipher)]['aead'] && strlen($tag) !== 16) {
            throw new DecryptException('Could not decrypt the data.');
        }

        if (!self::$supportedCiphers[strtolower($this->cipher)]['aead'] && is_string($tag)) {
            throw new DecryptException('Unable to use tag because the cipher algorithm does not support AEAD.');
        }
    }

    protected function shouldValidateMac(): bool
    {
        return !self::$supportedCiphers[strtolower($this->cipher)]['aead'];
    }
}
