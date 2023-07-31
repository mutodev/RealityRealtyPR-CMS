<?php

define("ENCRYPTION_KEY","Kshi_1-#Cacao__Mauro3");
define( "ENCRYPTION_ALGORITHM" , MCRYPT_BLOWFISH );
define( "ENCRYPTION_MODE"           , MCRYPT_MODE_ECB );

function encrypt( $text , $key = ENCRYPTION_KEY ){


    $algorithm      = ENCRYPTION_ALGORITHM;
    $algorithm_mode = ENCRYPTION_MODE;
    $iv_size        = mcrypt_get_iv_size( $algorithm , $algorithm_mode );
    $iv             = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $text           = strlen( $text ).chr(0).$text;
    $crypttext      = mcrypt_encrypt( $algorithm , $key, $text , $algorithm_mode ,  $iv );

    return base64_encode($crypttext);
}

function decrypt( $code , $key = ENCRYPTION_KEY ){

    $algorithm      = ENCRYPTION_ALGORITHM;
    $algorithm_mode = ENCRYPTION_MODE;
    $iv_size        = mcrypt_get_iv_size( $algorithm , $algorithm_mode );
    $iv             = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $code           = base64_decode($code);
    $plaintext      = mcrypt_decrypt( $algorithm , $key , $code , $algorithm_mode , $iv );
    $sizepos        = strpos( $plaintext , chr(0) );
    $size           = substr( $plaintext , 0 , $sizepos );

    return substr( $plaintext , $sizepos + 1 , $size );
}

/*
ende-class.php   encrypt decrypt class

*/
/*
	// to encrypt
	$RC4 = new truko;
	$password = $RC4 -> jhonson($var);
	// to decrypt
	$RC4 = new truko;
	$normal = $RC4 -> jhonson($var,"de");
	*/

class truko {
    var $passkey = 'Hybrid_ETX-555_SK420PPDIDX'; // llave para encriptar y decriptar los passwords


    function jhonson($data, $case="enc") {
        if ($case == 'de') $data = urldecode($data);
        $key[] = "";
        $box[] = "";
        $temp_swap = "";
        $pwd_length = 0;
        $pwd_length = strlen($this->passkey);
        for ($i = 0; $i <= 255; $i++) {
            $key[$i] = ord(substr($this->passkey, ($i % $pwd_length), 1));
            $box[$i] = $i;
        }                $x = 0;
        for ($i = 0; $i <= 255; $i++) {
            $x = ($x + $box[$i] + $key[$i]) % 256;
            $temp_swap = $box[$i];
            $box[$i] = $box[$x];
            $box[$x] = $temp_swap;
        }
        $temp = "";
        $k = "";
        $cipherby = "";
        $cipher = "";
        $a = 0;
        $j = 0;
        for ($i = 0; $i < strlen($data); $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $temp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $temp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipherby = ord(substr($data, $i, 1)) ^ $k;
            $cipher .= chr($cipherby);
        }
        if ($case == 'de') $cipher = urldecode(urlencode($cipher));
        else $cipher = urlencode($cipher);
        return $cipher;
    }
}

class encrypt_cc {
    var $passkey = 'Lm_CC_Card_Encripted_Ye@aA'; // llave para encriptar y decriptar los passwords


    function jhonson($data, $case="enc") {
        if ($case == 'de') $data = urldecode($data);
        $key[] = "";
        $box[] = "";
        $temp_swap = "";
        $pwd_length = 0;
        $pwd_length = strlen($this->passkey);
        for ($i = 0; $i <= 255; $i++) {
            $key[$i] = ord(substr($this->passkey, ($i % $pwd_length), 1));
            $box[$i] = $i;
        }                $x = 0;
        for ($i = 0; $i <= 255; $i++) {
            $x = ($x + $box[$i] + $key[$i]) % 256;
            $temp_swap = $box[$i];
            $box[$i] = $box[$x];
            $box[$x] = $temp_swap;
        }
        $temp = "";
        $k = "";
        $cipherby = "";
        $cipher = "";
        $a = 0;
        $j = 0;
        for ($i = 0; $i < strlen($data); $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $temp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $temp;
            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipherby = ord(substr($data, $i, 1)) ^ $k;
            $cipher .= chr($cipherby);
        }
        if ($case == 'de') $cipher = urldecode(urlencode($cipher));
        else $cipher = urlencode($cipher);
        return $cipher;
    }
}
?>
