<?php

namespace pwp;
use Medoo\Medoo;

class Model
{
    /**
     * Class constructor.
     */
    protected $pdo;
    protected $dsn;
    protected $config;
    public $version;
    public $database;
    protected $where=[];
    protected $join=[];
    protected $table_name;
    protected $field = '*';
    protected $data;
    protected $id;


    public function __construct($config=[])
    {
        if(sizeof($config)==0){
            $config=Config::getConfig();
        }else{
            $this->config = $config;
        }
        
        $this->database = new Medoo([
            'database_type'=> $config['db']['driver'],
            'database_name' => $config['db']['dbname'],
            'server' => $config['db']['host'],
            'username' => $config['db']['user_name'],
            'password' => $config['db']['password'],
            'charset' => $config['db']['charset'],
            'port' => $config['db']['port'],
            //'prefix' => $config['db']['prefix']
        ]);
        $this->getVersion();
    }

    protected function getVersion()
    {
        $info = $this->database->info();
        $this->version = $info['version'];
        return true;
    }

    public function select()
    {
        $result = $this->database->select($this->table_name, $this->join, $this->field, $this->where);
        if (empty($result)) {
            $result = [];
        }
        return $result;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function join($table_name, $data, $operator=">")
    {
        $key = '['.$operator.']'.$this->config['db']['prefix'].$table_name;
        $this->join[$key] = $data;
        //dump($this->join);
        return $this;
    }

    public function table($table_name)
    {
        $this->table_name = $this->config['db']['prefix'].$table_name;
        return $this;
    }

    public function field($field='*')
    {
        $this->field = $field;
        return $this;
    }

    public function order($order)
    {
        $this->where['ORDER'][] = $order;
        return $this;
    }

    public function group($group)
    {
        $this->where['GROUP'] = $group;
        return $this;
    }

    /**
     * limit限制函数
     *
     * @param array|string $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->where['LIMIT'] = $limit;
        return $this;
    }

    public function having($having)
    {
        $this->where['HAVING'] = $having;
        return $this;
    }

    public function data($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public function find()
    {
        $column=$this->database->get($this->table_name, $this->join,$this->field, $this->where);
        return $column;
    }

    public function add()
    {
        $status=$this->database->insert($this->table_name, $this->data);
        if (!$status) {
            var_dump($this->database->error());
        }
        $this->id = $this->database->id();
        return $status;
    }

    public function begin()
    {
        return $this->pdo->beginTransaction();
    }

    public function commit()
    {
        return $this->pdo->commit();
    }

    public function rollback()
    {
        return $this->pdo->rollBack();
    }

    public function getLastSql()
    {
        return $this->database->last();
    }

    public function update()
    {
        return $this->database->update($this->table_name,$this->data,$this->where);
    }

    public function delete(){
        $data = $this->database->delete($this->table_name,$this->where);
        return $data->rowCount();
    }

    public function query($sql){
        $sql = str_replace("{pre}",$this->config['db']['prefix'],$sql);
        $result = $this->database->query($sql);
        return $result;
    }

    public function count(){
        return $this->database->count($this->table_name,$this->join,$this->field,$this->where);
    }

    public function sum(){
        return $this->database->sum($this->table_name,$this->join,$this->field,$this->where);
    }

    public function avg(){
        return $this->database->avg($this->table_name,$this->join,$this->field,$this->where);
    }

    public function max(){
        return $this->database->max($this->table_name,$this->join,$this->field,$this->where);
    }

    public function min(){
        return $this->database->min($this->table_name,$this->join,$this->field,$this->where);
    }

    public function rand(){
        return $this->database->rand($this->table_name,$this->join,$this->field,$this->where);
    }
}