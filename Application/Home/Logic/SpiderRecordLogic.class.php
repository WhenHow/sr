<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/5/15
 * Time: 1:41
 */

namespace Home\Logic;


use Common\Model\SpiderRecordModel;

class SpiderRecordLogic
{
    public function __construct()
    {
    }

    public function getSpiderListByAppId($app_id,$page,$limit){
        $record_model = new SpiderRecordModel();
        $record_data = $record_model->getSpiderListByAppId($app_id,$page,$limit);
        $spider_list = M('spiders')->select();
        foreach ($record_data['data'] as &$val){
            $spider_data = list_search($spider_list, ['id'=>$val['spider_agent_id']]);
            if(!$spider_data){
                $val['spider_name'] = '未知';
            }else{
                $val['spider_name'] = $spider_data[0]['spider_name'];
            }
        }
        return $record_data;
    }
}