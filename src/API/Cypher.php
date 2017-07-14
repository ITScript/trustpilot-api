<?php

namespace ITS\Trustpilot\API;

class Cypher {
    /**
     * To get the keys, base64 decode the keys you copy from the Trustpilot site: base64_decode('dfkkdfj....');
     * The payload should be a JSON object with your order data:
     * $payload = [
     *     'email' => 'john@doe.com',
     *     'name'  => 'John Doe',
     *     'ref'   => '1234'
     * ];
     *
     * @param  string $payload
     * @param  string $encryptKey
     * @param  string $authKey
     * @return string
     */
    public static function encryptPayload($payload, $encryptKey, $authKey)
    {
        // Generate an Initialization Vector (IV) according to the block size (128 bits)
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-CBC'));

        //Encrypting the JSON with the encryptkey and IV with AES-CBC with key size of 256 bits, openssl_encrypt uses PKCS7 padding as default
        $payloadEncrypted = openssl_encrypt($payload, 'AES-256-CBC', $encryptKey, OPENSSL_RAW_DATA, $iv);

        //Create a signature of the ciphertext.
        $HMAC = hash_hmac('sha256', ($iv . $payloadEncrypted), $authKey, true);

        //Now base64-encode the IV + ciphertext + HMAC:
        $base64Payload = base64_encode(($iv . $payloadEncrypted . $HMAC));

        return urlencode($base64Payload);
    }
}