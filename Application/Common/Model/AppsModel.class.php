<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/3/6
 * Time: 22:47
 */

namespace Common\Model;


use Think\Model;

class AppsModel extends Model
{
    private $common_data_rule = [
        //检查app名
        array('app_name', '1,25', -1, self::EXISTS_VALIDATE, 'length'), //app名长度不合法
        //检查app创建人的id
        array('app_owner', 'isAppOwnerIdValidate', -2, self::EXISTS_VALIDATE, 'callback'), //app名长度不合法
    ];

    private $add_auto_rule = [
        array('add_time', NOW_TIME, self::MODEL_INSERT),
        array('app_status','getAppStatusBeforeAdd',self::MODEL_INSERT, 'callback'),
        array('app_id','generateAppId',self::MODEL_INSERT, 'callback'),
        array('app_key','generateAppKey',self::MODEL_INSERT, 'callback'),
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function addOne($data){
        $check_data['app_name'] = trim($data['app_name']);
        $check_data['app_owner'] = intval($data['app_owner']);
        $insert_data = $this->validate($this->common_data_rule)->auto($this->add_auto_rule)->create($check_data);
        if(!$insert_data){

        }
    }

    protected function trimRequire($content){
        if(trim($content) === ""){
            return false;
        }
        return true;
    }


    protected function isAppOwnerIdValidate($app_owner_id){
        if($app_owner_id <= 0 ){
            return false;
        }
        return true;
    }

    protected function isAppNameExist($app_name, $owner_id, $id = 0){
        $condition['app_name'] = $app_name;
        $condition['owner_id'] = $owner_id;
        if($id > 0){
            $condition['id'] = array('neq',$id);
        }
        $data = $this->where($condition)->find();
        return $data ? true : false;
    }


    protected function getAppStatusBeforeAdd(){
        $is_new_app_need_verify = C('APP_NEED_VERIFY');
        if($is_new_app_need_verify == 1){
            return 0;
        }
        return 1;
    }

    protected function generateAppId(){
        return mt_rand(10,99).md5(uniqid());
    }

    protected function generateAppKey(){
        return think_ucenter_md5(time(),mt_rand(10000,9999));
    }
}