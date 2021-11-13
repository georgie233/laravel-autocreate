<?php

namespace App\Http\Controllers\Auth\Api;

use App\Http\Controllers\BaseController;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    protected $tables = [
        'user' => ['users', 'account', 'password'],
    ];
    protected $tbName_model = [
          'users'=>User::class
    ];
    protected $LoginExcludeKey = [''];
    protected $RegisterExcludeKey = ['users'];
    //
    protected $tableName, $name, $password;

    public function login(Request $request, $key)
    {
        if ($t = $this->checking($request, $key, $this->LoginExcludeKey)) return $t;
        return (new AuthLogin())->login($request->all(),$this->tableName, $this->name, $this->password);
    }

    public function register(Request $request, $key)
    {
        if ($t = $this->checking($request, $key, $this->RegisterExcludeKey)) return $t;
        return (new  AuthLogin())->register($request->all(), $this->tableName, $this->name, $this->password);
    }

    //验证  请求参数，路径是否合法，数据库是否存在
    public function checking($request, $key, $exclude)
    {
        if (in_array($key, $exclude)) return $this->responseIllegal();//该key是否已被排除
        if (!$this->tables[$key]) return $this->responseIllegal();//是否注册了这个key
        $this->tableName = $tableName = $this->tables[$key][0];
        $this->name = $name = $this->tables[$key][1] ?? 'name';
        $this->password = $password = $this->tables[$key][2] ?? 'password';
        if (!(isset($request[$name]) && isset($request[$password]))) return $this->responseError('参数有误');//是否有参数
        if ((empty($request[$name]) || empty($request[$password]))) return $this->responseError('参数有误');//是否有参数
        if (!\Schema::hasTable($tableName)) return $this->responseError('系统错误');//是否有表
        return false;
    }

    public function getModel($tableName){
        return $this->tbName_model[$tableName];
    }
}
