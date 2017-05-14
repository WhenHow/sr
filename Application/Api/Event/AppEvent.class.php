<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/2
 * Time: 23:14
 */

namespace Api\Event;

use Common\Model\AppsModel;
use Common\Model\SpiderRecordModel;
use Common\Model\SpidersModel;

class AppEvent
{
    public function addOneSpiderRecord($data)
    {
        if (!$this->isSpiderRecordValidate($data)) {
            return makeFailedResponse('00011', getCodeMsg('00011'));
        }
        //获得app id
        $app_model = new AppsModel();
        $app_id = $app_model->getId($data['app_id'], $data['app_key']);
        if (!$app_id) {
            return makeFailedResponse('00010', getCodeMsg('00010'));
        }
        //获得spider_id
        $spider_model = new SpidersModel();
        $spider_id = $spider_model->getId($data['spider_type']);
        if (!$spider_id) {
            return makeFailedResponse('00012', getCodeMsg('00012'));
        }
        //添加一条蜘蛛记录
        $record['app_id'] = $app_id;
        $record['spider_agent_id'] = $spider_id;
        $record['spider_agent'] = $data['spider_agent'];
        $record['access_url'] = $data['access_url'];
        $spider_record_model = new SpiderRecordModel();
        $spider_record_model->addOne($record);

        return makeSuccessResponse($data);
    }

    private function isSpiderRecordValidate($record_data)
    {
        if (!$record_data) {
            return false;
        }
        $need_key = ['app_id', 'app_key', 'spider_type', 'spider_agent', 'access_url'];
        foreach ($need_key as $val) {
            if (!isset($record_data[$val])) {
                return false;
            }

            if (empty($record_data[$val])) {
                return false;
            }
        }

        return true;
    }
}
