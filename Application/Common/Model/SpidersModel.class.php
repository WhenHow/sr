<?php
/**
 * Created by PhpStorm.
 * User: xwh
 * Date: 2017/5/15
 * Time: 1:06
 */

namespace Common\Model;

use Think\Model;
class SpidersModel extends Model
{
    public function getId($spider_name){
        $where['spider_name'] = $spider_name;
        $ret = $this->where($where)->find();
        return $ret ? $ret['id'] : false;
    }
}