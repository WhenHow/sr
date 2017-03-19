<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/18
 * Time: 23:40
 */

namespace Home\Controller;


use Home\Logic\AppsLogic;

class HomePageController extends HomeController
{
    /**
     * 添加一个app
     */
    public function add(){
        $data['app_name'] = I('name');
        $add_ret = (new AppsLogic())->addOne($data,true);
        $this->ajaxReturn($add_ret);
    }

    /**
     * app的管理列表
     */
    public function appManage(){
        $page = intval(I('p'));
        $limit = intval(C('PAGE_LIMIT'));
        $my_apps = (new AppsLogic())->fetchMyAppList($page,$limit);
        $this->assign($my_apps);
        $this->display();
    }
}