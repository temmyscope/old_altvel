<?php
namespace app\model;

class Model{
	public const FETCH_ARRAY = 0;
	public const FETCH_OBJECT = 1; 
	public const FETCH_OBJECTS_ARRAY = 2; 
	/**
	* @var $_db stores database object
	* @var $_cache stores cache object
	* @var $_table string  refers to the name of the table peculiar to the model child class
	* @var $_fulltextColumn stores array of fulltext columns for optimized search query
	*/
	protected $_db, $cache, $_table, $_fulltextColumn;
	/**
	* @var $instance 
	* @var $_tbl stores static name of table from the late-bound model-child class
	*/
	public static $instance, $_tbl;

	/**
	* @var Array operators 
	* contains the list of acceptable sql operators
	*/
	private static $operators = [ '=', 'like', '!=', '<>', '<=', '>=', '>', '<', 'not' ];

	/**
	* @var Array<string> $control 
	* stores the control / clause / condition statements
	*/
	public static $control = [ 
		'columns' => '', 
		'where' => '' , 
		'groupby' => '' , 
		'orderby' => '', 
		'limit' => '', 
		'values' => []
	];

	public function __construct(){

	}

	public static function columns($cols = '*'){
		self::$control['columns'] .= (is_array($cols)) ? implode(',', $cols) : $cols;
		return static::$instance;
	}

	public static function All(){
		self::$control['columns'] .= '*';
		return static::$instance;
	}

	public static function where($column, $operator, $value){
		if (strtolower($operator) != 'in') {
			$operator = (in_array($operator, self::$operators)) ? $operator : '=';
			$where = $column." ".$operator." ". "?" ;
			self::$control['values'][] = $value;
		}else{
			$where = $column.' IN ( ' . implode(',', $value) . ' )';
		}
		self::$control['where'] .= $where;
		return static::$instance;
	}

	public static function and(){
		self::$control['where'] .= ' AND ';
		return static::$instance;
	}

	public static function or(){
		self::$control['where'] .= ' OR ';
		return static::$instance;
	}

	public static function orderby($order = []){
		self::$control['orderby'] .= implode(', ', $order);
		return static::$instance;
	}

	public static function groupby($group = []){
		self::$control['groupby'] .= implode(', ', $group);
		return static::$instance;
	}	

	public static function limit($limit, $offset = 0){
		$offset = "{$offset}, ";
		self::$control['limit'] .= $offset. $limit;
		return static::$instance;
	}

	public static function paginate($limit){
		(int)$m = Request::get('page') ?? 0;
		$offset = ($m > 0) ? (($m * $limit)-$limit ) . ", " : 0 . ", ";
		self::$control['limit'] .= $offset. $limit;
		return static::$instance;
	}

	/**
	* The final method to chain the eloquent sql construction takes optional parameter
	* @param $state carries the mode for fetching results;
	* default is <array>
	* or MODEL::FETCH_OBJECTS_ARRAY to get an array of multiple object classes
	* MODEL::FETCH_OBJECT returns object if the array result has a count of one
	*/
	public static function fetch($state = self::FETCH_ARRAY){
		$columns = (self::$control['columns'] != '') ? self::$control['columns'] : '*';
		if(empty(self::$control['where'])){
			unset(self::$control['where']);
		}else{
			rtrim(rtrim(self::$control['where'], 'AND '), 'OR ');
		}
		$res = self::DBInstance()->query(static::$_tbl, $columns, self::$control, self::$control['values']);
		if($state === MODEL::FETCH_OBJECT){
			return (object) $res[0];
		}elseif($state === MODEL::FETCH_OBJECTS_ARRAY){
			$new_class = [];
			foreach ($res as $key => $value) {
				$new_class[] = (object) $res[$key];
			}
			return $new_class;
		}
		self::$control = '';
		return $res;	
	}

	public static function DBInstance(){
		return new DB();
	}

	public function getColumns(){
    	return $this->_db->showColumns($this->_table);
  	}

  	public function IfColumnExists($columnName): bool{
  		return (in_array( $columnName, (array)$this->_table )) ? true : false ;
  	}

  	public function DataExists($column, $data): bool{
  		return $this->_db->CheckIfExists($this->_table, $column, $data);
  	}

  	public function softDataExists($column, $data) : bool{
  		return $this->_db->softCheckIfExists($this->_table, $column, $data);
  	}

  	public function search($search_term, array $columns){
  		$data = $this->_db->search($this->_table, $search_term, $columns, $this->_fulltextColumn);
  		return $data;
  	}

	public function findById($id){
		return $this->_db->findById($this->_table, $id);
	}

	public function findBy($params = []){
		$data = $this->_db->findBy($this->_table, $params, $columns='*');
		return $data;
	}

	public function findAll(){
		return $this->_db->selectAll($this->_table);
	}

	public function loadAll($params = [], $values = []){
		return $this->_db->query($this->_table, '*', $params, $values);
	}

	public function findfirst($params = []){
		$data = $this->findBy($params);
		if (!empty($data)){
			return (object) $data[0];
		}
	}

	public function save(...$args){
		return (count($args) == 2) ? $this->update($args[0], $args[1]) : $this->insert($args[0]);
	}

	public function insert($params){
		return $this->_db->insert($this->_table, $params);
	}

	public function count($obj = '*', $params){
		return $this->_db->counter($this->_table, $obj, $params);
	}

	public function update($params, $clause){
		return $this->_db->update($this->_table, $params, $clause);
	}

	public function softDelete($id){
		return $this->_db->update($this->_table, ['deleted' => 'true'], $id);
	}

	public function delete($id){
		return $this->_db->delete($this->_table, $id);
	}

	public function softdeleteBy($param){
		return $this->_db->softdeleteBy($this->_table, $param);
	}

	public function query($columns='*', $params= [], $values = []){
		return $this->_db->query($this->_table, $columns, $params, $values );
	}
	/*
	| The raw group of methods process queries without using tables registered with the Called Child Model
	*/
	public function rawQuery($table, $columns='*', $params= [], $values = []){
		return $this->_db->query($table, $columns, $params, $values );
	}

	public function rawInsert($table, $params){
		return $this->_db->save($table, $params);
	}

	public function rawUpdate($table, $params){
		return $this->_db->update($table, $params, $clause);
	}

	public function rawSoftdeleteBy($table, $params){
		return $this->_db->softdeleteBy($table, $param);
	}
}