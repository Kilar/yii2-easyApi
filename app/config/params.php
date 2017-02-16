<?php
use common\lib\helpers\SysHelper;

return [
    'adminEmail' => SysHelper::getEnv('mail_username'),
];
