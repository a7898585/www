<?php

namespace Common\Model;

use Think\Model;

final class AreaModel extends Model {

    final public function province() {
        $data = F('province');
        if (empty($data)) {
            $data = $this->field('area_id as id,name_simple as name,pinyin')->where(array('level' => '2'))->order('sort')->select();
            F('province', $data);
        }
        return $data;
    }

    final public function city($id) {
        $data = F('city_' . $id);
        if (empty($data)) {
            $data = $this->field('area_id as id,name_simple as name,pinyin')->where(array('parent_id' => $id))->order('sort')->select();
            F('city_' . $id, $data);
        }
        return $data;
    }

    /**
     * 根据area_id
     * @param type $aid  area_id
     * @return type
     */
    final public function getAreaName($aid) {
        $res = $this->getInfoByAreaId($aid);
        return $res['name_full'];
    }

    final public function getSimplePinyin($aid) {
        $res = $this->getInfoByAreaId($aid);
        return $res['pinyin'];
    }

    final public function getList($where) {
        return $this->field('*')->where($where)->select();
    }

    /**
     * 根据id获取简写名称
     * @param type $id area_id
     */
    public function getSimpleName($id) {
        $res = $this->getInfoByAreaId($id);
        return $res['name_simple'];
    }

    /**
     * 获取省份/城市信息
     * @param type $id  area_id
     * @return type
     */
    function getInfoByAreaId($id) {
//        $res = F('AreaInfoByAreaid_' . $id);
        if (empty($res)) {
            $res = $this->field("id,area_id,parent_id,name_full,name_simple,pinyin")->where(array("area_id" => $id))->find();
            F('AreaInfoByAreaid_' . $id, $res);
        }
        return $res;
    }

    /**
     * 城市详情
     * @param int $limit
     * @return mixed
     */
    public function getInfoByName($city_name) {
        $key = 'AreaInfoByName_' . md5($city_name);
        $res = F($key);
        if (empty($res)) {
            $res = $this->field('name_simple,area_id,name_full,id,pinyin,is_status')->where(array('name_simple' => $city_name))->find();
            F($key, $res);
        }
        return $res;
    }

    /**
     * 城市详情
     * @param $pinyin pinyin
     * @return mixed
     */
    public function getInfoByPinyin($pinyin) {
        $key = 'AreaInfoByPinyin_' . $pinyin;
        $res = F($key);
        if (empty($res)) {
            $res = $this->field('name_simple,area_id,name_full,id,pinyin,is_status')->where(array('pinyin' => $pinyin))->find();
            F($key, $res);
        }
        return $res;
    }

    /**
     * 热门城市
     * @param int $limit
     * @return mixed
     */
    public function getListByHot($limit = 5) {
        return $this->field('name_full,name_simple,pinyin,pinyin_f,sort,is_status,is_hot')->where(array('is_hot' => 1))->limit($limit)->order('sort asc')->select();
    }

    /**
     * 选择城市页面
     * @return array|mixed
     */
    public function getCityList() {
        $key = 'CityList';
        $res = F($key);
        if (empty($res)) {
            $temp = $this->field('id,name_simple as name,pinyin,pinyin_f,sort,is_status,is_hot')->order('pinyin_f,sort asc')->where("id in(1,2,3,4) OR (level=3 and pinyin_f<>'' and is_leaf=0)")->select();
            if (is_array($temp)) {
                $res = array();
                foreach ($temp as $item) {
                    $res[strtoupper($item['pinyin_f'])][] = $item;
                }
                F($key, $res);
            }
        }
        return $res;
    }

}

?>