<?php


namespace App\Http\Controllers\Auth\Api;


use App\LoginToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
//处理登录注册逻辑
class AuthLogin
{
    protected $type = 'json';

    public function setType($type){
        $this->type = $type;
    }

    protected function LoginTokenInsert($table,$id,$token){
        return LoginToken::create([
            'model'=>$table,
            'login_id'=>$id,
            'token'=>$token
        ]);
    }

    //$data 处理数据  $table 表名称   $name 登录字段名   $pwd 登录验证字段
    public function login($data,$table,$name='name',$pwd='password'){
        try {
            $user = DB::table($table)->where($name,$data[$name])->first();
            if (!$user)return $this->ResponseJson([],'该账号不存在',403);
            $user = (array)$user;
            $bool = Hash::check($data[$pwd],$user[$pwd]);
            if (!$bool)return $this->ResponseJson([],'密码错误',403);
            $token = md5(md5($user['id'].$user[$name]).str_random(5).time()).str_random(5);
            $t = $this->LoginTokenInsert($table,$user['id'],$token);
            if ($t)
                return $this->ResponseJson(['token'=>$token],'登录成功',200);
            else return $this->ResponseJson([],'登录失败',403);
        }catch (\Exception $exception){
            if ($exception->getCode() < 10){
                return $this->ResponseJson([],$exception->getMessage(),$exception->getCode());
            }
            return $this->ResponseJson([],'注册失败',403);
        }
    }
    public function register($data,$table,$name='name',$pwd='password',$is_login = true){
        try {
            $d = $data;
            $d[$pwd] = bcrypt($d[$pwd]);
            if (DB::table($table)->where($name,$data[$name])->count())throw new \Exception('账户名已存在',1);
            $user = DB::table($table)->insert($d);
            if (!$user)return $this->ResponseJson([],'注册失败',1);
            if ($is_login){
                $this->setType('');
                $token = $this->login($data,$table,$name,$pwd);
                $token = isset($token['data']['token'])?$token['data']['token']:'';
                return $this->ResponseJson(['token'=>$token],'注册成功',200);
            }
            return $this->ResponseJson([],'注册成功',200);

        }catch (\Exception $exception){
            if ($exception->getCode() < 10){
                return $this->ResponseJson([],$exception->getMessage(),$exception->getCode());
            }
//            dd($exception);
            if ($exception->getCode() == '42S22')
                return $this->ResponseJson([],'参数不匹配',1);
            return $this->ResponseJson([],'注册失败',403);
        }
    }

    protected function ResponseJson($data = [],$msg = [],$code){
        if ($this->type !== 'json')return ['code'=>$code,'data'=>$data,'msg'=>$msg];
        return response()->json([
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        ], 200);
    }
}
