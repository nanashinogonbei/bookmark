<?php

function h( $str )
{
    return htmlspecialchars( $str, ENT_QUOTES, 'UTF-8' );
}

// ハッシュ化して返す
function ch( $str )
{
    return password_hash( $str, PASSWORD_DEFAULT );
}

// ランダムな文字列を返す
function getRandomTxt ( $length = 32 )
{
    return array_reduce( range(1, $length), function($p){ return $p.str_shuffle('1234567890abcdefghijklmnopqrstuvwxyz.,;:{}[]-^=?#$%&')[0]; } );
}

?>