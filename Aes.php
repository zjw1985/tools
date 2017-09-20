<?php
/**
 * 
 **/
class Aes
{

	const HAX_IV = '00000000000000000000000000000000';
	CONST KEY    = 'b4212@7Dc8d985dA9%f&#0e3c35a209a';

	/**
	 * AES 加密
	 * @param type $enodeStr
	 * @param type $key
	 * @return type
	 */
	public static function encrypt($str, $key = null)
	{
	    $hex_iv = SELF::HAX_IV;
	    $key = $key ? $key : SELF::KEY;
	    $key = hash('sha256', $key, true);
	    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

	    // dehexiv
	    $dehexiv = '';
	    for ($i = 0; $i < strlen($hex_iv) - 1; $i += 2) {
	        $dehexiv .= chr(hexdec($hex_iv[$i] . $hex_iv[$i + 1]));
	    }

	    mcrypt_generic_init($td, $key, $dehexiv);
	    $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    // PKCS7 补码
	    $pad = $block - (strlen($str) % $block);
	    $str .= str_repeat(chr($pad), $pad);
	    $encrypted = mcrypt_generic($td, $str);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	    return base64_encode($encrypted);
	}

	/**
	 * AES 解密
	 *
	 * @param type $input
	 * @param type $key
	 * @return type
	 */
	public static function decrypt($code, $key = null)
	{
	    $hex_iv = SELF::HAX_IV;
	    $key = $key ? $key : SELF::KEY;
	    $key = hash('sha256', $key, true);
	    $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

	    // dehexiv
	    $dehexiv = '';
	    for ($i = 0; $i < strlen($hex_iv) - 1; $i += 2) {
	        $dehexiv .= chr(hexdec($hex_iv[$i] . $hex_iv[$i + 1]));
	    }

	    mcrypt_generic_init($td, $key, $dehexiv);
	    $str = mdecrypt_generic($td, base64_decode($code));
	    mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);

	    // 去除补码
	    $slast = ord(substr($str, - 1));
	    $slastc = chr($slast);
	    if (preg_match("/$slastc{" . $slast . "}/", $str)) {
	        $str = substr($str, 0, strlen($str) - $slast);
	        return $str;
	    } else {
	        return false;
	    }
	}
}


$param = ['user_id'=>12,'contents'=>'aae'];
$param = json_encode($param);
$strEncrypt = urlencode(AES::encrypt($param));
echo "加密后数据：<br>";
echo $strEncrypt;
echo "<br>";
$request = AES::decrypt(urldecode($strEncrypt));
$request = json_decode($request, true);
echo "解密后的数据：<br>";
var_dump($request);