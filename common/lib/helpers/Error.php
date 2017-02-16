<?php
namespace common\lib\helpers;

/**
 * 系统错误码类
 * @author Hym
 */
class Error
{
    const API_NOTFOUND  = 101;
    const INFO_NOTFOUND = 102;
    const REQUEST_ERROR = 103;
    const FILE_NOTFOUND = 104;
    const SERVER_ERROR  = 105;
    const PARAMS_ERROR  = 106;
    const VERSION_ERROR = 107;
    const TIME_ERROR    = 108;
    const SIGN_FAIL     = 109;
    const LOGIN_FAIL    = 110;
    const TOKEN_FAILURE = 111;
    const CLASS_ERROR   = 112;
}