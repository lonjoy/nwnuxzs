<?php
include 'lanewechat.php';
use LaneWeChat\Core\Menu;

/**
 * 自定义菜单
 */
//设置菜单
$menuList = array(

	array('id'=>'1', 'pid'=>'0', 'name'=>'教务助手', 'type'=>'', 'code'=>''),

    array('id'=>'2', 'pid'=>'1', 'name'=>'成绩查询', 'type'=>'click', 'code'=>'成绩查询'),

    array('id'=>'3', 'pid'=>'1', 'name'=>'课表查询', 'type'=>'click', 'code'=>'课表查询'),

    array('id'=>'4', 'pid'=>'1', 'name'=>'学分绩点', 'type'=>'click', 'code'=>'学分绩点'),

    array('id'=>'5', 'pid'=>'1', 'name'=>'密码修改', 'type'=>'click', 'code'=>'密码修改'),

    array('id'=>'6', 'pid'=>'0', 'name'=>'校园生活', 'type'=>'', 'code'=>''),

    array('id'=>'7', 'pid'=>'1', 'name'=>'吐槽社区', 'type'=>'view', 'code'=>'http://www.lanecn.com'),

    array('id'=>'8', 'pid'=>'1', 'name'=>'失物招领', 'type'=>'view', 'code'=>'lane_wechat_menu_3'),
);



echo "ok !";
\LaneWeChat\Core\Menu::setMenu($menuList);
//获取菜单
//\LaneWeChat\Core\Menu::getMenu();
//删除菜单
//\LaneWeChat\Core\Menu::delMenu();