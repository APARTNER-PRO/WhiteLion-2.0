<?php

class sodium {

    /**
     * Get a secret key for encrypt/decrypt
     *
     * Use libsodium to generate a secret key.  This should be kept secure.
     *
     * @return string
     * @see encrypt(), decrypt()
     */
    public static function generateSecretKey($password = null)
    {
        if(is_null($password))
            return sodium_crypto_secretbox_keygen();
        return hash('sha256', $password, true);
    }

    /**
    * Encrypt a message
    *
    * @param string $message - message to encrypt
    * @param string $key - encryption key
    * @return string
    */
    function encrypt($message, $key)
    {
        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $cipher = base64_encode(
            $nonce.
            sodium_crypto_secretbox(
                $message,
                $nonce,
                $key
            )
        );
        sodium_memzero($message);
        sodium_memzero($key);
        return $cipher;
    }

    /**
    * Decrypt a message
    *
    * @param string $encrypted - message encrypted with safeEncrypt()
    * @param string $key - encryption key
    * @return string
    */
    function decrypt($encrypted, $key)
    {  
        $decoded = base64_decode($encrypted);
        if ($decoded === false) {
            throw new Exception('Scream bloody murder, the encoding failed');
        }
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
            throw new Exception('Scream bloody murder, the message was truncated');
        }
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
        $ciphertext = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        $plain = sodium_crypto_secretbox_open(
            $ciphertext,
            $nonce,
            $key
        );
        // if ($plain === false) {
        //      throw new Exception('the message was tampered with in transit');
        // }
        sodium_memzero($ciphertext);
        sodium_memzero($key);
        return $plain;
    }

}