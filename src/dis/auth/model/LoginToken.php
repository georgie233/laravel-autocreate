<?php

namespace App;

use App\Http\Controllers\Auth\Api\AuthController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class LoginToken extends Model
{
    //
    protected $table = "login_token";
    protected $fillable = ['login_id', 'model', 'token'];
    protected $expirationMinute = 60 * 24 * 7;//7天

    protected $err = '';

    public function checkToken($token)
    {
        $d = $this->getTokenData($token);
        return $d && $d['valid'];
    }

    public function getTokenData($token)
    {
        $data = $this->where('token', $token)->orderBy('created_at','desc')->first();
        $this->err = "获取不到";
        if (!$data) return false;
        $c = $data['created_at'];
        $now = date('Y-m-d H:i:s', time());
        $m = floor((strtotime($now) - strtotime($c)));
        $this->err = "token已过期";
        $data['valid'] = $m < $this->expirationMinute;
        return $data;
    }

    public function getModel($token)
    {
        $data = $this->getTokenData($token);
        $tb_name = $data['model'];
        $c = new AuthController();
        $model = $c->getModel($tb_name);
        if (!$model) return "没有配置";
        $t = (new $model)->find($data['login_id']);
        if (!$t) $this->err = '获取不到';
        return $t;
    }

    public function getErr()
    {
        return $this->err;
    }
}
