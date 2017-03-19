<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/18
 * Time: 23:45
 */

namespace Home\Logic;


use Common\Model\AppsModel;
use Think\Page;

class AppsLogic
{
    public function __construct()
    {
    }

    /**
     * 添加一个app
     * @param array $app_data
     * @param bool $is_current_app_owner
     * @return array|string
     */
    public function addOne($app_data = array(), $is_current_app_owner = true)
    {
        if ($is_current_app_owner) {
            $current_user = getCurrentUser();
            if (!$current_user) {
                return makeFailedResponse('00004', getCodeMsg('00004'), false);
            }
            $app_data['app_owner'] = $current_user['uid'];
        }

        $app_data['app_name'] = htmlspecialchars(trim($app_data['app_name']));
        $app_data['app_owner'] = intval($app_data['app_owner']);
        return (new AppsModel())->addOne($app_data);
    }

    public function fetchMyAppList($page = 0, $limit = 0,$customer_map = null){
        $page = intval($page);
        $limit = intval($limit);

        $user = getCurrentUser();
        $app_owner_id = intval($user['uid']);
        $condition['owner_id'] = $app_owner_id;

        if($customer_map){
            $condition = array_merge($condition, $customer_map);
        }
        $data = M('apps')->where($condition)->page($page)->limit($limit)->order('id desc')->select();
        $count = M('apps')->where($condition)->count();
        $page_obj = new Page($count,$limit);

        return ['data'=>$data,'page_obj'=>$page_obj->show(),'count'=>$count];
    }
}