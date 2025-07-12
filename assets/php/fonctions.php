<?php
    function encrypt($data, $key) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }
    
    function decrypt($data, $key) {
        list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
        return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
    }
    
    $key = 'ecrivez_ici_votre_cle_de_chiffrement';


    //Mot de passe automatique
    function generateRandomString($length) {
        $characters = '0123456789#@-_ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $ms_code = '';
        $maxCharIndex = strlen($characters) - 1;
    
        for ($i = 0; $i < $length; $i++) {
            $ms_code .= $characters[rand(0, $maxCharIndex)];
        }
    
        return $ms_code;
    }
    $ms_code = generateRandomString(12);

    // Transformation en lien
    function makeLinksClickable($text) {
        $text = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
            $text
        );
        return $text;
    }
?>