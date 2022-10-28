<?php

namespace Payon\PaymentGateway;

class PayonEncrypto
{
    private $_MC_ENCRYPT_KEY;

    public function __construct($key = "")
    {
        $this->_MC_ENCRYPT_KEY = $key;
    }


    function Encrypt($text)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $this->_MC_ENCRYPT_KEY . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        return base64_encode('Salted__' . $salt . openssl_encrypt($text . '', 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv));
    }

    function Decrypt($encrypted)
    {
        $encrypted = base64_decode($encrypted);
        $salted = substr($encrypted, 0, 8) == 'Salted__';
        if (!$salted) {
            return null;
        }
        $salt = substr($encrypted, 8, 8);
        $encrypted = substr($encrypted, 16);
        $salted = $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $this->_MC_ENCRYPT_KEY . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        return openssl_decrypt($encrypted, 'aes-256-cbc', $key, true, $iv);
    }
}
