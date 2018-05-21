<?php
class Encryption
{
    function encode($text)
    {
        return base64_encode($text);
    }

    function decode($text)
    {
        return base64_decode($text);
    }
}
?>