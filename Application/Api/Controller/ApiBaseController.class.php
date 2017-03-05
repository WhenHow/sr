<?php
namespace Api\Controller;

use Think\Controller;
use Think\Exception;

/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/2
 * Time: 23:00
 */
class ApiBaseController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    protected function ApiMethodDispatcher()
    {
        $param = $this->fetchParam();
        if ($param === false) {
            $this->ajaxReturn(makeFailedApiResponse('BAD_PARAM'));
        }
        $api_function = $param['func'];
        $param = $param['data'];
        $api_map = C('API_MAP');
        $function_str = $api_map[$api_function];

        $function_array = explode('@', $function_str);
        $function_class_name = $function_array[0];
        $function_name = $function_array[1];

        try {
            $function_class = new $function_class_name();
            $ret = $function_class->$function_name($param);
        }catch (\Exception $e){
            $this->ajaxReturn(makeFailedApiResponse('BAD_PARAM'));
        }

        $this->ajaxReturn(json_encode($ret));
    }

    private function parseApiParam($param)
    {
        $param = rc4(C('API_RC4_KEY'), base64_decode($param));
        if (!$param) {
            return false;
        }

        $param = json_decode($param, true);
        if (!$param) {
            return false;
        }

        return $param;

    }

    private function isApiParamValidate($param)
    {
        if (empty($param)) {
            return false;
        }
        if (!isset($param['func']) || !$param['func']) {
            return false;
        }

        $api_map = C('API_MAP');
        $func = $param['func'];
        if (!isset($api_map[$func])) {
            return false;
        }

        if (!isset($param['data'])) {
            return false;
        }

        return true;
    }

    private function fetchParam()
    {
        $param = I('data');
        $param = $this->parseApiParam($param);

        if ($param === false) {
            return false;
        }

        if ($this->isApiParamValidate($param) === false) {
            return false;
        }

        return $param;
    }


}
