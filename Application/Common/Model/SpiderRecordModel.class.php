<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/27
 * Time: 1:45
 */

namespace Common\Model;


use Think\Model;
use Think\Page;

class SpiderRecordModel extends Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加一条爬取记录
     * @param $data
     * @return array|null|string
     */
    public function addOne($data)
    {
        $add_rule = [
            array('app_id', 'isAppIdValidate', '00008', self::MUST_VALIDATE, 'callback'),
            array('spider_agent_id', 'isSpiderIdValidate', '00007', self::MUST_VALIDATE, 'callback'),
            array('access_url','require','00009',self::MUST_VALIDATE),
        ];
        $data = $this->validate($add_rule)->create($data);
        if(!$data){
            $error_code = $this->getError();
            return makeFailedResponse($error_code, getCodeMsg($error_code),false);
        }
        $data['atime'] = time();
        $data['add_year'] = intval(date('Y',$data['atime']));
        $data['add_month'] = intval(date('m',$data['atime']));
        $data['add_day'] = intval(date('d',$data['atime']));
        $data['add_hour'] = intval(date('H',$data['atime']));

        $add_ret = $this->add($data);
        if($add_ret){
            $data['id'] = intval($add_ret);
            return makeSuccessResponse($data,false);
        }
        return makeFailedResponse('00006',L('00006'),false);
    }

    /**
     * appid 是否合法
     * @param $app_id
     * @return bool
     */
    public function isAppIdValidate($app_id)
    {
        $app_id = intval($app_id);
        if ($app_id <= 0) {
            return false;
        }
        $where['id'] = $app_id;
        $where['app_status'] = 1;
        $ret = M('apps')->where($where)->find();
        if (!$ret) {
            return false;
        }
        return true;
    }

    /**
     *
     * @param $spider_id
     * @return bool
     */
    public function isSpiderIdValidate($spider_id)
    {
        $spider_id = intval($spider_id);
        if ($spider_id <= 0) {
            return false;
        }
        $spider_list = getAllSpiderList();
        if (key_exists($spider_id, $spider_list)) {
            return true;
        }
        return false;
    }


    public function getSpiderListByAppId($app_id,$page,$limit){
        $app_id = intval($app_id);
        $condition['app_id'] = $app_id;

        $data = $this->where($condition)->page($page)->limit($limit)->order('id desc')->select();
        $count = $this->where($condition)->count();
        $page_obj = new Page($count,$limit);

        return ['data'=>$data,'page_obj'=>$page_obj->show(),'count'=>$count];

    }
}