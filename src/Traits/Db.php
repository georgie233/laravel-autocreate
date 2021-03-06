<?php
namespace Georgie\AutoCreate\Traits;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

trait Db
{
    protected $denyColumn = ['id', 'created_at', 'updated_at'];

    protected function getColumnData()
    {
        $columns = $this->listTableColumns();
        $configs = [];
        foreach ($columns as $column) {
            if ($this->allowColumn($column)) {
                $configs[$column->getName()] = [
                    'name' => $column->getName(),
                ];
            }
        }

        return $configs;
    }

    protected function allowColumn($column)
    {
        return ! in_array($column->getName(), $this->denyColumn);
    }

    protected function getDoctrineConnection()
    {
        $config           = new Configuration();
        $connectionParams = [
            'dbname'   => config('database.connections.mysql.database'),
            'user'     => config('database.connections.mysql.username'),
            'password' => config('database.connections.mysql.password'),
            'host'     => config('database.connections.mysql.host'),
            'driver'   => 'pdo_mysql',
            'charset'  => config('database.connections.mysql.charset'),
        ];

        return DriverManager::getConnection($connectionParams, $config);
    }

    protected function getTableComment($model)
    {
        $res  = \DB::select("show create table ".$model->getTable());
        $info = ((array)$res[0]);
        preg_match("@COMMENT='(.*?)'@i", $info['Create Table'], $match);

        return isset($match[1]) ? trim($match[1]) : '';
    }

    protected function listTableColumns()
    {
        return $this->getDoctrineConnection()->getSchemaManager()->listTableColumns($this->modelInstance->getTable());
    }

    protected function isTable()
    {
        $tables = $this->getDoctrineConnection()->getSchemaManager()->listTables();
        foreach ($tables as $table) {
            if ($this->model->getTable() == $table->getName()) {
                return true;
            }
        }

        return false;
    }

    public function formatColumns()
    {
        $columns = $this->listTableColumns();
        $data    = [];
        foreach ($columns as $column) {
            $data[$column->getName()] = $this->formatComment($column);
        }

        return $data;
    }

    public function getListColumns()
    {
        return $this->formatColumns(null, $this->listShowFields);
    }

    /**
     * ????????????????????????????????????
     *
     * @param $column
     *
     * @return array
     */
    protected function formatComment($column)
    {
        $comment = $column->getComment();
        $info    = [];
        if ( ! is_null($comment)) {
            $options = explode('|', $comment);
            if (count($options) >= 2) {
                $info['title']   = $options[0];
                $info['name']    = $column->getName();
                $info['nonull']  = $column->getNotNull();
                $info['default'] = $column->getDefault();
                $info['options'] = $this->formatFieldOptions($options);
            }
        }

        return $info;
    }

    protected function formatFieldOptions($options)
    {
        if (isset($options[2])) {
            $info = [];
            foreach (explode(',', $options[2]) as $k => $option) {
                $tmp           = explode(':', $option);
                $info[$tmp[0]] = $tmp[1];
            }
            $options[2] = $info;
        }

        return $options;
    }
}
