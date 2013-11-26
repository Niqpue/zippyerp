<?php

namespace ZippyERP\System\Pages;

use \ZippyERP\System\User;
use \ZippyERP\System\DB;
use \ZippyERP\System\System;
use \Zippy\Html\DataList\DataView;

class Users extends AdminBase
{

        // public $userlist = array();

        public function __construct()
        {
                parent::__construct();

                $this->add(new DataView("userrow", new UserDataSource(), $this, 'OnAddUserRow'))->Reload();

                $this->add(new DataView("rolerow", new \ZCL\DB\EntityDataSource('\ZippyERP\System\Role'), $this, 'OnAddRoleRow'))->Reload();
        }

        public function afterRequest()
        {
                parent::afterRequest();
                $this->getComponent('userrow')->Reload();
        }

        //удаление  юзера
        public function OnRemove($sender)
        {
                $user = $sender->getOwner()->getDataItem();
                User::delete($user->user_id);
                //  $user = $sender->getOwner()->getDataItem();
                //  $user->active = 1 - $user->active;
                //  $user->save();
        }

        public function OnAddUserRow($datarow)
        {
                $item = $datarow->getDataItem();
                $datarow->add(new \Zippy\Html\Link\RedirectLink("userlogin", '\\ZippyERP\\System\\Pages\\UserInfo', $item->user_id))->setValue($item->userlogin);

                $datarow->add(new \Zippy\Html\Label("created", date('d.m.Y', $item->registration_date)));
                $datarow->add(new \Zippy\Html\Link\ClickLink("remove", $this, "OnRemove"))->setVisible($item->userlogin != 'admin');
                return $datarow;
        }

        public function OnAddRoleRow(\Zippy\Html\DataList\DataRow $datarow)
        {
                $item = $datarow->getDataItem();
                $datarow->add(new \Zippy\Html\Label("description", $item->description));

                $users = $datarow->add(new \Zippy\Html\Link\LinkList("users", ', '));

                $userlist = User::find('user_id  in (select user_id from system_user_role where role_id  = ' . $item->role_id . ')');

                foreach ($userlist as $user) {
                        $users->AddRedirectLink('\\ZippyERP\\Pages\\System\\UserInfo', array($user->user_id), $user->userlogin);
                }
        }

}

class UserDataSource implements \Zippy\Interfaces\DataSource
{

        //private $model, $db;

        public function getItemCount()
        {
                return User::findCnt();
        }

        public function getItems($start, $count, $sortfield = null, $desc = true)
        {
                return User::find('', $sortfield . ($desc === true ? ' desc' : ''), null, $count, $start);
        }

        public function getItem($id)
        {
                return User::load($id);
        }

}

