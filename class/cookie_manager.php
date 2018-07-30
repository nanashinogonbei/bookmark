<?php

class cookieManager
{
	protected $cookie;

	
	public function __construct()
	{

    }
	
    // 指定した名前のcookieが存在したら$cookieに代入、存在しなければ生成して$cookieに代入
	public function checkCookie( $cookie_name )
	{
        if( isset($_COOKIE[$cookie_name]) )
        {
            return true;
        }
        else
        {
            return false;
        }
	}

    // cookieの値を返す
	public function returnCookie()
	{
        return $this->cookie;
	}
	
}

?>