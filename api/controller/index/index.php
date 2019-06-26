<?php
namespace controller\index;

use lib\coreController;
use lib\coreModel;

class index extends coreController {

    private $coreModel;

    public function __construct()
    {
        crossDomain();
        $this->coreModel = new coreModel();
    }

    public function index() {
        ajax(200, '接口访问成功了！', []);
    }

    public function addHouse()
    {
        $this->param('userid,houseTitle,houseSubtitle');
        $addHouse = $this->coreModel->table('house')->mode('insert')->data([
            'create_user' => $this->params['userid'],
            'create_time' => date('Y-m-d H:i:s'),
            'title' => $this->params['houseTitle'],
            'subtitle' => $this->params['houseSubtitle'],
            'valid' => 1
        ])->query();

        if ($addHouse) {
            ajax(200, '成功');
        } else {
            ajax(400, '添加失败');
        }
    }

    public function getHouseList()
    {
        $houseList = $this->coreModel->table('house')->mode('select')->field('*')->where("valid=1")->order(['create_time DESC'])->query();
        if (!empty($houseList)) {
            $houseIds= implode(',', array_column($houseList, 'id'));
            $userids= "'".implode("','", array_column($houseList, 'create_user'))."'";
            $foodCount = $this->coreModel->table('list')->mode('select')->field('house_id,COUNT(id) AS c')->group('house_id')->where("house_id IN ({$houseIds}) AND valid=1")->query();
            $houseIdToFoodCount = array_key_values('house_id', $foodCount);
            $userCount = $this->coreModel->table('list')->mode('select')->field('house_id,COUNT(DISTINCT userid) AS c')->group('house_id')->where("house_id IN ({$houseIds}) AND valid=1")->query();
            $houseIdToUserCount = array_key_values('house_id', $userCount);
            $userInfo = $this->coreModel->table('user')->mode('select')->field('*')->where("id IN ({$userids})")->query();
            $useridToUserinfo = array_key_values('id', $userInfo);
            foreach ($houseList as $k=>$v) {
                $houseList[$k]['foodCount'] = $houseIdToFoodCount[$v['id']]['c'];
                $houseList[$k]['userCount'] = $houseIdToUserCount[$v['id']]['c'];
                $houseList[$k]['username'] = $useridToUserinfo[$v['create_user']]['name'];
            }

            ajax(200, '成功', $houseList);
        } else {
            ajax(201, '没有任何数据');
        }

    }

    public function addFoodToHouse()
    {
        $this->param('houseId,userid,foodTitle,foodSubtitle');
        $ifHouseExist = $this->coreModel->table('house')->mode('select')->where("id={$this->params['houseId']}")->query();
        if (empty($ifHouseExist)) {
            ajax(400, '这个房间id不存在');
        }
        $addFood = $this->coreModel->table('list')->mode('insert')->data([
            'house_id' => $this->params['houseId'],
            'userid' => $this->params['userid'],
            'title' => $this->params['foodTitle'],
            'subtitle' => $this->params['foodSubtitle'],
            'create_time' => date('Y-m-d H:i:s'),
            'valid' => 1
        ])->query();
        var_dump($this->coreModel->sql());
        if ($addFood) {
            ajax(200, '成功');
        } else {
            ajax(400, '添加失败');
        }
    }

    public function getFoodList()
    {
        $this->param('houseId');
        $ifHouseExist = $this->coreModel->table('house')->mode('select')->where("id={$this->params['houseId']} AND valid=1")->query();
        if (empty($ifHouseExist)) {
            ajax(400, '这个房间id不存在');
        }

        $foodList = $this->coreModel->table('list')->mode('select')->where("house_id={$this->params['houseId']} AND valid=1")->order(['create_time DESC'])->query();
        $userids = "'".implode("','", array_column($foodList, 'userid'))."'";
        $userInfo = $this->coreModel->table('user')->mode('select')->field('*')->where("id IN ({$userids})")->query();
        $useridToUserinfo = array_key_values('id', $userInfo);
        if ($foodList) {
            foreach ($foodList as $k => $v) {
                $foodList[$k]['username'] = $useridToUserinfo[$v['userid']]['name'];
            }
            ajax(200, '成功', $foodList);
        } else {
            ajax(201, '没有任何数据');
        }
    }

    public function addUser()
    {
        $this->param('userid,name');
        $ifUserExist = $this->coreModel->table('user')->mode('select')->where("id='{$this->params['userid']}'")->query();
        if (empty($ifUserExist)){
            $addUser = $this->coreModel->table('user')->mode('insert')->data([
                'id' => $this->params['userid'],
                'name' => $this->params['name'],
                'create_time' => date('Y-m-d H:i:s')
            ])->query();

            if ($addUser) {
                ajax(200, '成功');
            } else {
                ajax(400, '操作失败');
            }
        } else {
            ajax(400, '该用户已存在');
        }
    }

    public function getUser()
    {
        $this->param('userid');
        $ifUserExist = $this->coreModel->table('user')->mode('select')->where("id='{$this->params['userid']}'")->query();
        if (empty($ifUserExist)){
            ajax(200, '该用户不存在');
        } else {
            ajax(200, '该用户已存在');
        }
    }

    public function delHouse()
    {
        $this->param('houseId');
        $delHouse = $this->coreModel->table('house')->mode('update')->data([
            'valid' => 0
        ])->where("id={$this->params['houseId']}")->query();
        if ($delHouse) {
            ajax(200, '成功');
        } else {
            ajax(400, '失败');
        }
    }

    public function delEat()
    {
        $this->param('eatId');
        $delEat = $this->coreModel->table('list')->mode('update')->data([
            'valid' => 0
        ])->where("id={$this->params['eatId']}")->query();

        if ($delEat) {
            ajax(200, '成功');
        } else {
            ajax(400, '失败');
        }
   }
}
