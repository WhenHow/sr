<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/5
 * Time: 23:38
 */

namespace Api\Controller;


class SpiderRecController extends ApiBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function entry(){
        $this->ApiMethodDispatcher();
    }

    public function test(){
        $param['func'] = 'ADD_SPIDER_RECORD';
        $param['data'] = array('a'=>1,'b'=>2);
        $param_str = base64_encode(rc4(C('API_RC4_KEY'), json_encode($param)));
        echo $param_str;
    }
}