<?php
/*
Plugin Name: spider recorder
Plugin URI: baidu.com
Description: 蜘蛛统计
Version: 0.01
Author: dingwenjie
Author URI: baidu.com
*/
define('SPIDER_RECORDER_PLUGIN_DIR', plugin_dir_path(__FILE__));

class dwjSpiderRecorder
{
    private $search_engine_map = null;

    public function __construct()
    {
        $search_engine_map_path = SPIDER_RECORDER_PLUGIN_DIR . '/search_engine_map.php';
        if (file_exists($search_engine_map_path)) {
            $this->search_engine_map = require($search_engine_map_path);
        }

    }

    public function recordSpider()
    {
        $request_url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $search_engine = $this->judgeSearchEngine();
        if (!$search_engine) {
            return;
        }
        $this->addRecordToMainSite($search_engine, $request_url);
    }

    private function judgeSearchEngine()
    {
        if (!$this->search_engine_map) {
            return "";
        }

        $agent_str = strtolower($_SERVER['HTTP_USER_AGENT']);
        //测试在这里
        $agent_str .= "google";
        //测试在这里
        foreach ($this->search_engine_map as $key => $val) {
            if (strpos($agent_str, (string)$key) !== false) {
                return $val;
            }
        }

        return "";
    }

    private function getSrConfig()
    {
        static $sr_config = [];
        $config_path = SPIDER_RECORDER_PLUGIN_DIR . '/config.php';
        if (!$sr_config && file_exists($config_path)) {
            $sr_config = require($config_path);
        }
        return $sr_config;
    }

    private function addRecordToMainSite($spider, $url)
    {

        if (!function_exists('curl_init')) {
            return false;
        }

        $config = $this->getSrConfig();
        if (!$config) {
            return false;
        }

        $main_site_address = $config['main_site_address'];

        $data['app_id'] = $config['app_id'];
        $data['app_key'] = $config['app_key'];
        $data['spider_type'] = $spider;
        $data['spider_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data['access_url'] = $url;

        $api_string = $this->buildApiString($config['add_record_func_name'], $data);
        if (!$api_string) {
            return false;
        }


        try {
            $response = $this->http($main_site_address, ['data'=>$api_string], "POST");
        } catch (Exception $e) {
            return false;
        }
        return $response;

    }

    private function buildApiString($func_name, $data)
    {
        $config = $this->getSrConfig();
        if (!$config['rc4_key']) {
            return "";
        }

        $param['func'] = $func_name;
        $param['data'] = $data;
        $param_str = base64_encode($this->rc4($config['rc4_key'], json_encode($param)));
        return $param_str;
    }

    private function http($url, $params, $method = 'GET', $header = array(), $multi = false)
    {
        $opts = array(
            CURLOPT_TIMEOUT => 5,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER => $header
        );
        /* 根据请求类型设置特定参数 */
        switch (strtoupper($method)) {
            case 'GET':
                $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
                break;
            case 'POST':
                //判断是否传输文件
                $params = $multi ? $params : http_build_query($params);
                $opts[CURLOPT_URL] = $url;
                $opts[CURLOPT_POST] = 1;
                $opts[CURLOPT_POSTFIELDS] = $params;
                break;
            default:
                throw new Exception('不支持的请求方式！');
        }
        /* 初始化并执行curl请求 */
        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) throw new Exception('请求发生错误：' . $error);
        return $data;
    }

    /*
 * rc4加密算法
 * $pwd 密钥
 * $data 要加密的数据
 */
    private function rc4($pwd, $data)//$pwd密钥　$data需加密字符串
    {
        $key[] = "";
        $box[] = "";
        $cipher = "";
        $pwd_length = strlen($pwd);
        $data_length = strlen($data);

        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }

        return $cipher;
    }
}


add_action('wp_loaded', [(new dwjSpiderRecorder()), 'recordSpider']);
