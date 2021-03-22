<?php
/**
  * ScoreUserInfo is a helper class for loading a user's information.
  *
  * Cannabis PowerScore, by the Resource Innovation Institute
  * @package  resourceinnovation/cannabisscore
  * @author  Morgan Lesko <rockhoppers@runbox.com>
  * @since 0.2
  */
namespace ResourceInnovation\CannabisScore\Controllers;

use App\Models\RIIUserInfo;
use App\Models\User;
use ResourceInnovation\CannabisScore\Controllers\ScoreCollector;

class ScoreUserInfo extends ScoreCollector
{
    public $id           = 0;
    public $usrInfoID    = 0;
    public $name         = '';
    public $email        = '';
    public $company      = '';
    public $slug         = '';
    public $level        = 0;
    public $levelDef     = 0;

    public function loadUser($userID = 0, $user = null, $company = '')
    {
        if (!$user && !isset($user->id)) {
            $user = User::find($userID);
        }
        if (isset($user->id)) {
            $this->id    = $user->id;
            $this->name  = $user->name;
            $this->email = $user->email;
        } else {
            $this->id = $userID;
        }
        $info = RIIUserInfo::where('usr_user_id', $this->id)
            ->first();
        if ((!$info || !isset($info->usr_user_id) || intVal($info->usr_user_id) <= 0)
            && isset($user->email)) {
            $info = RIIUserInfo::where('usr_invite_email', $user->email)
                ->first();
            if ($info
                && isset($this->id)
                && (!isset($info->usr_user_id) || intVal($info->usr_user_id) <= 0)) {
                $info->usr_user_id = $this->id;
                $info->save();
            }
        }
        return true;
    }

}

