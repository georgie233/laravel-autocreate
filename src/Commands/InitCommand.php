<?php


namespace Georgie\AutoCreate\Commands;


class InitCommand extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:init';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化各种配置';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        $this->handleVue();
    }

    protected function handleVue()
    {
        try {
            //初始化vue main.js
            $u = __DIR__ . '/../resources/vue_cli/src/main.ts';
            $url = base_path('vue-cli/src/main.ts');
            $content = file_get_contents($u);
            file_put_contents($url, $content);

        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
