<?php

namespace Georgie\AutoCreate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'g:auth {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'copy auth {name?}';

    protected $module;
    protected $name;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucfirst($this->argument('name')) ?? 'name';
        $this->name = $name == '' ? 'name' : strtolower($name);
        $this->copyMigrations();
        $this->copyView();
        $this->alterController();
        $this->alterModel();
        $this->alterView();
    }

    protected function alterController()
    {
        $login = app_path("Http/Controllers/Auth/LoginController.php");
        $str = file_get_contents($login);
        $str2 = <<<str
    public function username()
    {
        return "{$this->name}";
    }
}
str;
        if (strstr($str, 'username()') == '')
            $str = substr($str, 0, strrpos($str, '}')) . $str2;
        $str = str_replace("protected \$redirectTo = '/home';", "protected \$redirectTo = '/admin';", $str);
        file_put_contents($login, $str);
        $this->info("alter LoginController complete!");

        $re = app_path("Http/Controllers/Auth/RegisterController.php");
        $str = file_get_contents($re);
        $str = str_replace("protected \$redirectTo = '/home';", "protected \$redirectTo = '/admin';", $str);
        $str = str_replace("'name' => ['required', 'string', 'max:255'],", "'nick_name' => ['required', 'string', 'unique:users']", $str);
        $str = str_replace("'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],", "'{$this->name}' => ['required', 'string', 'unique:users']", $str);
        $str = str_replace("'name' => \$data['name']", "'nick_name' => \$data['nick_name']", $str);
        $str = str_replace("'email' => \$data['email'],", "'{$this->name}' => \$data['{$this->name}'],", $str);
//        $this->info($str);
        file_put_contents($re, $str);
        $this->info("alter RegisterController complete!");
    }

    protected function alterModel()
    {
        $file = app_path("User.php");
        $str = file_get_contents($file);
        $str = str_replace("'email'", " '{$this->name}', 'nick_name'", $str);
        file_put_contents($file, $str);
        $this->info("");
    }

    protected function copyView()
    {
        $a = __DIR__ . '/../dis/auth/view';
        $b = base_path("resources/views/auth");
        $this->xCopy($a, $b, 1);
    }

    protected function alterView()
    {
        //登录模板
        $file = base_path("resources/views/auth/login.blade.php");
        $arr = [$this->name];
        $str = "";
        foreach ($arr as $item) {
            $str .= <<<str

        <div class="form-group row">
                <label for="{$item}"
                       class="col-md-4 col-form-label text-md-right">{$item}</label>
                <div class="col-md-6">
                    <input id="{$item}" type="text"
                           class="form-control{{ \$errors->has('{$item}') ? ' is-invalid' : '' }}"
                           name="{$item}" required>

                    @if (\$errors->has('{$item}'))
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ \$errors->first('{$item}') }}</strong>
                    </span>
                    @endif
                </div>
        </div>
str;
        }
        $file_str = file_get_contents($file);
        $file_str = str_replace("Georgie_insert",$str,$file_str);
        file_put_contents($file,$file_str);


        //注册模板
        $file = base_path("resources/views/auth/register.blade.php");
        $arr = [$this->name,'nick_name'];
        $str = "";
        foreach ($arr as $item) {
            $str .= <<<str

        <div class="form-group row">
            <label for="{$item}"
                   class="col-md-4 col-form-label text-md-right">{$item}</label>

            <div class="col-md-6">
                <input id="{$item}" type="text"
                       value="{{old('{$item}')}}"
                       class="form-control{{ \$errors->has('{$item}') ? ' is-invalid' : '' }}"
                       name="{$item}" required>

                @if (\$errors->has('{$item}'))
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ \$errors->first('{$item}') }}</strong>
                </span>
                @endif
            </div>
        </div>
str;
        }
        $file_str = file_get_contents($file);
        $file_str = str_replace("Georgie_insert",$str,$file_str);
        file_put_contents($file,$file_str);
    }

    protected function copyMigrations()
    {
        //复制迁移文件
        $a = __DIR__ . '/../dis/auth/migrations';
        $b = base_path("database/migrations");
        $this->xCopy($a, $b, 1,'.php');
        $this->info("alter User complete!");

        if ($this->name != "name") {
            $file = base_path("database/migrations/2014_10_12_000000_create_users_table.php");
            $str = file_get_contents($file);
            $str = str_replace("\$table->string('name')->unique();", "\$table->string('{$this->name}')->unique();");
            file_put_contents($file, $str);
        }
    }


    //copy fun
    protected function xCopy($source, $destination, $child = 1,$ex='')
    {//用法：
        // xCopy("feiy","feiy2",1):拷贝feiy下的文件到 feiy2,包括子目录
        // xCopy("feiy","feiy2",0):拷贝feiy下的文件到 feiy2,不包括子目录
        //参数说明：
        // $source:源目录名
        // $destination:目的目录名
        // $child:复制时，是不是包含的子目录

        if (!is_dir($source)) {
            echo("Error:the $source is not a direction!");
            return 0;
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0777);
        }

        $handle = dir($source);
        while ($entry = $handle->read()) {
            if (($entry != ".") && ($entry != "..")) {
                if (is_dir($source . "/" . $entry)) {
                    if ($child)
                        $this->xCopy($source . "/" . $entry, $destination . "/" . $entry, $child,$ex);
                } else {
                    copy($source . "/" . $entry, $destination . "/" . $entry.$ex);
                }
            }
        }
        //return 1;
    }

    protected function copyFiles($bool = false)
    {
        if ($bool) {
            //copy layouts
            $a = __DIR__ . '/../dis/views/layouts';
            $b = \Module::getModulePath('Admin') . 'Resources/views/layouts';
            $this->xCopy($a, $b, 1);
            return $this->info("copy complete!");
        }
        //copy Admin/网页模板文件
        $files = glob(__DIR__ . '/../dis/views/layouts/*.php');
        foreach ($files as $file) {
            $to = \Module::getModulePath('Admin') . 'Resources/views/layouts/' . basename($file);
            if (is_file($to)) {
                $this->info($to . " is exists");
                continue;
            }
            file_put_contents($to, $file);
            $this->info("{$to} file create successfully");
        }
    }

    protected function copyPublicFiles()
    {
        //copy public
        $a = __DIR__ . '/../dis/public';
        $b = public_path('');
        $this->xCopy($a, $b, 1);
    }

    protected function copyLang()
    {
        //copy lang
        $a = __DIR__ . '/../dis/lang';
        $b = base_path('resources/lang');
        $this->xCopy($a, $b, 1);
    }
}
