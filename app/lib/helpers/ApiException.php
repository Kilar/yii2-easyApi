<?php
namespace app\lib\helpers;

class ApiException extends \yii\base\Exception
{
    public $statusCode;
    
    public function __construct($code, $message = null)
    {
        $this->statusCode = $code;
        parent::__construct($message, $code);
    }
    
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'ApiException';
    }
    
    
}