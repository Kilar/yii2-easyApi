<?php
namespace common\lib\interfaces;


interface SignEncryption
{
    public static function generateSign($data, $key);
}