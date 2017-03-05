<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/2
 * Time: 23:11
 */
return [
    'API_MAP' => [
        'APP_LOGIN' => 'Api\Event\AppEvent@login',
        'ADD_SPIDER_RECORD' => 'Api\Event\AppEvent@addOneSpiderRecord'
    ],

    'API_RC4_KEY' => 'THIS IS SPIDER',

    'ERROR_API_CODE_MAP' => [
        'UNKNOWN_ERROR' => '000001',
        'BAD_PARAM' => '000002',
    ],
];
