<?php
namespace app\model;

use \PDO;
use \PDOException;

class DB{

/*----------------------------------------------------------------------------------------------
|		The pdo access caller is executed immediately the class is constructed					|
-----------------------------------------------------------------------------------------------*/
	public function __construct(){																
		$this->connect();																		
	}																							

  
/*----------------------------------------------------------------------------------------------
|										The Pdo Access is setup									|
-----------------------------------------------------------------------------------------------*/
	private function connect(){																	
		try{
			if(strpos(DB_TYPE, 'sqlite') !== false){
				$this->pdo = new PDO(DB_TYPE.":".DB_HOST);
			}else{
				$this->pdo = new PDO(DB_TYPE.":host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASSWORD);	
			}
		    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);				
		    $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);			
		}catch(PDOException $e){																
		    echo "Connection failed: " . $e->getMessage();										
		}																						
	}	

	public function pdo(){																	
		return $this->pdo;																		
	}																							

/*----------------------------------------------------------------------------------------------
|		This function is used to get all data from a selected table except {deleted}			|
-----------------------------------------------------------------------------------------------*/
	public function get($table, $columns='*', $conditionString, array $params){					
		$stmt = $this->pdo->prepare("SELECT {$columns} FROM {$table} WHERE {$conditionString} AND deleted != 'true'");	
		if($stmt->execute($params)){															
			return $stmt->fetchAll(PDO::FETCH_ASSOC);												
		}																						
	}

	public function checkIfExists($table, $column, $data): bool{
		$stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE {column} = ?");
		if($stmt->execute($data)){
			return (empty($stmt->fetchAll(PDO::FETCH_ASSOC))) ? false : true ;			
		}
	}

	public function softCheckIfExists($table, $column, $data): bool{
		$stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE {column} = ? AND deleted != 'true' ");
		if($stmt->execute($data)){
			return (empty($stmt->fetchAll(PDO::FETCH_ASSOC))) ? false : true ;			
		}
	}

/*----------------------------------------------------------------------------------------------|
|		This function is used to get union data from selected tables except {deleted}			|
|	$tables= [], $columns= [], $params = [ 'where'=> [ [], [] ], 'groupby' => '', 'limit'=> ]	|
-----------------------------------------------------------------------------------------------*/
	public function union(array $tables, $columns= ['*'], $params =[]){
		$str = "";
		$columns = implode(',', $columns);
		$values = [];																
		foreach($tables as $table){
			$str .= "SELECT {$columns} FROM {$table}";															
			if (array_key_exists('where', $params)) {
				if(is_array($params['where'])){
					$len = count($params['where']);
					foreach($params['where'] as $k => $v){
						foreach ($k as $key => $val) {
							$str .= " WHERE $key = ? AND";
							array_push($values, $val);
						}
					}
					$str = rtrim($sql, ' AND'). " AND deleted != 'true'"; 							
				}else{																			
					$str .= " deleted != 'true'";							
				}
			}
			$str .= " UNION "; 
		}
		$str= rtrim($str, ' UNION ');
		if(array_key_exists('groupby', $params)){
			$str .= " GROUP BY {$params['groupby']}";														
		}
		if(array_key_exists('orderby', $params)){
			$str .= " ORDER BY {$params['orderby']}";														
		}
		if(array_key_exists('limit', $params)){												
			$sql .= " LIMIT {$params['limit']}";												
		}
		$stmt = $this->pdo->prepare($str);														
		if($stmt->execute($params)){															
			return $stmt->fetchAll(PDO::FETCH_ASSOC);											
		}																						
	}

/*----------------------------------------------------------------------------------------------
|								For building select queries										|
-----------------------------------------------------------------------------------------------*/
	public function query($table, $columns = '*', $params = [], $values =[]){
		$sql = "SELECT {$columns} FROM {$table}";												
		if (array_key_exists('where', $params)) {		
			$sql .= " WHERE";																	
			if (is_array($params['where'])) {													
				foreach ($params['where'] as $key => $value) {
					$sql .= " {$key}  = ? AND";													
				}																				
				$sql = rtrim($sql, ' AND'); 							
			}else {
				$sql .= " {$params['where']}";						
			}
			$sql .= " AND deleted != 'true'";																			
		}																						
		if (array_key_exists('groupby', $params) && !empty($params['groupby'])) {
			$sql .= " GROUP BY {$params['groupby']}";				
		}
		if (array_key_exists('orderby', $params) && !empty($params['orderby'])) {												
			$sql .= " ORDER BY {$params['orderby']}";											
		}																						
		if (array_key_exists('limit', $params) && !empty($params['limit'])) {												
			$sql .= " LIMIT {$params['limit']}";												
		}
		$stmt = $this->pdo->prepare($sql);
		if($stmt->execute($values)){															
			return $stmt->fetchAll(PDO::FETCH_ASSOC);											
		}																						
	}																							

/*----------------------------------------------------------------------------------------------
|								For building select queries										|
-----------------------------------------------------------------------------------------------*/
	public function selectALL($table){															
		return $this->select($table);															
	}

/*----------------------------------------------------------------------------------------------
|					The only select query capable of extracting a deleted data 					|
-----------------------------------------------------------------------------------------------*/
	public function getAll($table){																
		$st = $this->pdo->prepare("SELECT * FROM {$table} ORDER BY id DESC");					
  		if($st->execute()){																		
			return $st->fetchAll(PDO::FETCH_ASSOC);												
		}																						
	}																							

	public function select($table, $columns = '*'){																
		$st = $this->pdo->prepare("SELECT {$columns} FROM {$table} WHERE deleted != 'true' ORDER BY id DESC");	
  		if($st->execute()){																						
			return $st->fetchAll(PDO::FETCH_ASSOC);																
		}																										
	}
																								
	public function selectBY($table, $params = [], $columns = '*'): array{				
		$conditionString = '';																	
		foreach($params as $key => $value){														
			$conditionString .= " {$key} = ? AND";												
		}$conditionString = rtrim($conditionString, ' AND');									
		$st = $this->pdo->prepare("SELECT {$columns} FROM {$table} WHERE {$conditionString} AND deleted != 'true'");
		$values = array_values($params);														
  		if($st->execute($values)){																
			return $st->fetchAll(PDO::FETCH_ASSOC);												
		}																						
	}																							
																								
	public function findBy($table, $params = [], $columns = '*'){								
		return $this->selectBY($table, $params, $columns);										
	}																							

	public function findById($table, $id){														
		$st = $this->pdo->prepare("SELECT * FROM {$table} WHERE id= ? AND deleted != 'true'");	
		if($st->execute(array($id))){															
			return $st->fetchAll(PDO::FETCH_ASSOC);												
		}																						
	}																							
																								
	public function selectById($table, $id){													
		return $this->findById($table, $id);													
	}																							

/*----------------------------------------------------------------------------------------------
|		    	Search method capable of serach regular, unique & fulltext columns				|
-----------------------------------------------------------------------------------------------*/
																								
  	public function search($table, $query, array $columns, $fulltext = []){					
  		$new_query = "%".$query."%";															
  		$where = '';																			
  		$values = [];																			
  		if(!empty($fulltext)){																	
  			foreach ($fulltext as $key){														
  				$where .= "MATCH ({$key}) AGAINST (?) OR ";										
  			}																					  			
  			$values[] =  $query;																
  		}																						
  		foreach($columns as $column){															
  			$where .= "$column LIKE ? OR ";														
  		}																						
  		$where = rtrim($where, ' OR ');															
  		$values = array_fill(count($values), count($columns), $new_query);						
  		$st = $this->pdo->prepare("SELECT * FROM {$table} WHERE {$where} AND deleted != 'true'");	
  		if($st->execute($values)){																
			return $st->fetchAll(PDO::FETCH_ASSOC);												
		}																						
  	}
																													
	public function showColumns($table){																				
    	$query = $this->pdo->prepare("SHOW COLUMNS FROM {$table}");														
  		return $query->fetchALL(PDO::FETCH_OBJ);																		
	}																													


/*----------------------------------------------------------------------------------------------
|			The function counts using the COUNT sql												|
-----------------------------------------------------------------------------------------------*/
	public function counter($table, $obj = "*", $params = []){					
		$sql = "SELECT COUNT({$obj}) as count FROM {$table} WHERE deleted != 'true'";
		$values = [];
		if (!empty($params)) {											
			foreach($condition as $key => $value){												
				$sql .= " AND {$key} = ?";											
			}
			$values = array_values($params);
		}																						
		$stmt = $this->pdo->prepare($sql);														
		if ($stmt->execute($values)){															
			return $stmt->fetch(PDO::FETCH_OBJ);												
		}																						
	}																							

/*----------------------------------------------------------------------------------------------
|																								|
-----------------------------------------------------------------------------------------------*/
	public function insert($table, $params){													
		$columns= implode(', ', array_keys($params));											
		$placeholder= implode(', ', array_fill(0, count($params), '?'));							
		$val = array_values($params);															
		$st = $this->pdo->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholder})");
		if($st->execute($val)){																	
			return $this->pdo->lastInsertID();													
		}
		return false;																					
	}																							
/*----------------------------------------------------------------------------------------------
|			Save data in the database, based on whether or not an id was passed					|
-----------------------------------------------------------------------------------------------*/
	public function save($table, $params, $id = 0){												
		return ( is_int($id) && $id > 0) ? $this->update($table, $params, $id) :  $this->insert($table, $params);	
	}																							
/*----------------------------------------------------------------------------------------------
|																								|
-----------------------------------------------------------------------------------------------*/
	public function update(string $table, array $params, $condition): bool{					
		$str = '';																				
		foreach ($params as $key => $value) {													
			$str .= "{$key} = ?, ";																
		}$str = rtrim($str, ', ');																
																								
		if(is_array($condition)){																
			$values = array_merge(array_values($params), array_values($condition));				
			$conditionString = '';														
			foreach($condition as $key => $value){												
				$conditionString .= "{$key} = ? AND ";											
			}																				
			$conditionString = rtrim($conditionString, ' AND');									
		}else{																					
			$conditionString = "id = ?";														
			$values = array_values($params);													
			array_push($values, $condition);													
		}																						
		$st = $this->pdo->prepare("UPDATE {$table} SET {$str} WHERE {$conditionString}");
		if($st->execute($values)){																
			return true;																		
		}																						
		return false;																			
	}																							

/*----------------------------------------------------------------------------------------------
|	This Function deletes totally, data from a selected table									|
-----------------------------------------------------------------------------------------------*/
	public function delete($table, $id): bool{													
		$st = $this->pdo->prepare("DELETE FROM {$table} WHERE id = ?");							
		if($st->execute(array($id))){		
			return true;																		
		}																						
		return false;																			
	}

/*----------------------------------------------------------------------------------------------
|			soft deletes a column uusing parameters other than id								|
-----------------------------------------------------------------------------------------------*/										
	public function softDeleteBy($table, $param): bool{												
		return $this->update($table, ['deleted' => 'true'], $param);																				
	}																							

/*----------------------------------------------------------------------------------------------
|	This function sets the delete column of a table to true [making it inaccessible]			|
-----------------------------------------------------------------------------------------------*/
	public function softDelete($table, $id): bool{												
		return $this->update($table, ['deleted' => 'true'], $id);								
	}
}