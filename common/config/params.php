<?php
use common\lib\helpers\SysHelper;

return [
    'adminEmail' => SysHelper::getEnv('mail_username'),
    'supportEmail' => SysHelper::getEnv('mail_username'),
    'user.passwordResetTokenExpire' => 3600,
];
