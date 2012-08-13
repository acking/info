<?php
class mysql{
	private $_db;
	private $_link;
	static private $_instance=null;
	private function __construct($config){
			try {
				$dsn="{$config['dbms']}:host={$config['dbhost']};dbname={$config['dbname']}";
				$this->_db = new PDO($dsn, $config['dbuser'], $config['dbpwd'], array(PDO::ATTR_PERSISTENT => true));
				//echo "连接成功<br/>";
				//$this->_db = null;
			} catch (PDOException $e) {
				return false;
					// die ("Error!: " . $e->getMessage() . "<br/>");
			}
			$this->query("set names 'utf8'");
		
	}
	static function getInstance($config){
		if(self::$_instance==null){
		$class=__CLASS__;
		self::$_instance=new $class($config);
		}
		return self::$_instance;
	}
	/*
	*PDO基本链接对象
	*
	*/
	public function getDb(){
		return $this->_db;
	}
	/*
	*PDO基本执行方法exec
	*@sql 数据库执行语句
	*/
	public function exec($sql){
		return $this->_db->exec($sql);
	}
	/*
	*PDO基本执行方法query
	*@$sql String 数据库执行语句
	*/
	public function query($sql){
		return $this->_db->query($sql);
	}
	/*
	*统一执行方法
	*@$sql String 数据库执行语句
	*/
	public function execute($sql){
		$writeSQL=array("insert","update","delete");
		$str=strtolower(substr(trim($sql),0,6));
		if(in_array($str,$writeSQL)){
			return $this->exec($sql);		
		}
		if($str=="select"){
			return $this->query($sql);
		}
	}
	/*
	*获取最后插入ID
	*
	*/
	public function insertId(){
		return $this->_db->lastInsertId();
	}
	/*
	*获取总条数
	*
	*/
	public function rowCount($sql){
		$result=$this->query($sql);	
		return $result->rowCount();
	}
	/*
	*获取所有数据
	*@$sql  String 数据库语句
	*@$result_type 数据数组累心类型
	*/
	public function fetchAll($sql,$result_type=PDO::FETCH_ASSOC){
		$rs=$this->execute($sql);
		$rs->setFetchMode($result_type);
		$result_arr = $rs->fetchAll();
		return $result_arr;
	}
	/*
	*获取一条数据
	*@sql   String 数据库语句
	*@result_type 数据数组累心类型
	*/
	public function fetchOne($sql,$result_type=PDO::FETCH_ASSOC){
		$rs=$this->execute($sql);
		$rs->setFetchMode($result_type);
		$result_arr = $rs->fetch();
		return $result_arr;
	}
	/*
	*插入一条数据
	*@$table String 数据表名
	*@$data Array 插入数组的数组  数组 键对应数据库字段名 值对应插入的值
	*/
	public function insert($table,$data){
		$fields = array_keys($data);
		$values = array_values($data);
		foreach($fields as $key => $field){
			if($key == 0){
				$fieldStr = $field;
				$valueStr = "'" . mysql_escape_string($values[$key]) . "'";
			} else {
				$fieldStr .= "," . $field;
				$valueStr .= ",'" . mysql_escape_string($values[$key]) . "'";
			}
		}
		$sql = "insert {$table}({$fieldStr}) values({$valueStr})";
		return $this->execute($sql);
	}
	/*
	*更新一条数据
	*@$table String	数据表名
	*@$data  Array 插入数组的数组  数组 键对应数据库字段名 只对应插入的值
	*@$where String 更新语句的定位条件
	*/
	public function update($table,$data,$where=""){
		$set="";
		$where=$where?" where ".$where:"";
		foreach($data as $field=>$value){
			$str=$set?",":"";
			$set.=$str.$field." ='".$value."' ";
		}
		$sql="update {$table} set ".$set.$where;
		return $this->execute($sql);
	}	
	/*
	*删除数据
	*@$table String	数据表名
	*@$where String 删除的定位条件
	*/
	public function delete($table,$where=''){
		$where = $where == '' ? '' : " where " . $where;
		$sql = "delete from {$table}{$where}";
		return $this -> execute($sql);
	}
	/*
	*查询数据
	*@$table String	数据表名
	*@$fields Array 查询的字段名 空为*
	*@$where String 查询的定位条件
	*@$join String 查询的外联条件
	*@$limit String 查询的Limit条件
	*/
	public function findALL($table,$fields=array(),$where='',$join='',$limit='',$order="",$group=""){
		$fields = !$fields ? '*' : join(',',$fields);
		$where  = $where == '' ? ''  : ' where ' . $where;
		$limit  = $limit == '' ? ''  : ' limit ' . $limit;
		$order  = $order == '' ? ''  : ' order by ' . $order;
		$group  = $group == '' ? ''  : ' group by ' . $group;
		$sql = 'select ' . $fields . ' from ' . $table ." ". $join." ".$where ." ".$group ." ".$order ." ". $limit;
		return $this -> fetchAll($sql);
	}
	public function find($table,$fields=array(),$where='',$join='',$limit='',$order="",$group=""){
		$fields = !$fields ? '*' : join(',',$fields);
		$where  = $where == '' ? ''  : ' where ' . $where;
		$limit  = $limit == '' ? ''  : ' limit ' . $limit;
		$order  = $order == '' ? ''  : ' order by ' . $order;
		$group  = $group == '' ? ''  : ' group by ' . $group;
		$sql = 'select ' . $fields . ' from ' . $table ." ". $join." ".$where ." ".$group ." ".$order ." ".$limit;
		return $this -> fetchOne($sql);
	}
	
	public function selectALL($table,$condition){
		$condition=$condition->toArray();
		foreach($condition as $key=>$val){
			$conditionArr[$key]=$val;
		}
		foreach($condition['params'] as $key=>$val){
			// $condition['condition']=str_replace($key,$val,$condition['condition']);
			$condition['condition']=preg_replace("/{$key}/",$val,$condition['condition'],1);
		}
		$condition['condition']=$condition['condition']?" where ".$condition['condition']:"";
		$limit  = $condition['limit'] == '' ? ''  : ' limit ' . $condition['limit'];
		$order  = $condition['order'] == '' ? ''  : ' order by ' . $condition['order'];
		$group  = $condition['group'] == '' ? ''  : ' group by ' . $condition['group'];
		$sql = 'select ' . $condition['select'] . ' from ' . $table ." ". $condition['join'] ." ".$condition['condition'] ." ".$group." ".$order ." ". $limit;
		return $this -> fetchAll($sql);
	}
	function countByStr($table,$fields=array(),$where='',$join='',$limit='',$order="",$group=""){
		$fields = !$fields ? '*' : join(',',$fields);
		$where  = $where == '' ? ''  : ' where ' . $where;
		$limit  = $limit == '' ? ''  : ' limit ' . $limit;
		$order  = $order == '' ? ''  : ' order by ' . $order;
		$group  = $group == '' ? ''  : ' group by ' . $group;
		$sql = 'select ' . $fields . ' from '.$table ." ". $join." ".$where ." ".$group ." ".$order ." ". $limit;
		$result=$this->query($sql);
		return $result->rowCount();
	}
	function count($table,$condition){
		$condition=$condition->toArray();
		foreach($condition as $key=>$val){
			$conditionArr[$key]=$val;
		}
		foreach($condition['params'] as $key=>$val){
			// $condition['condition']=str_replace($key,$val,$condition['condition']);
			$condition['condition']=preg_replace("/{$key}/",$val,$condition['condition'],1);
		}
		$condition['condition']=$condition['condition']?" where ".$condition['condition']:"";
		$limit  = $condition['limit'] == '' ? ''  : ' limit ' . $condition['limit'];
		$order  = $condition['order'] == '' ? ''  : ' order by ' . $condition['order'];
		$group  = $condition['group'] == '' ? ''  : ' group by ' . $condition['group'];
		$sql = 'select ' . $condition['select'] . ' from '. $table ." ". $condition['join'] ." ".$condition['condition'] ." ".$group." ".$order ." ". $limit;
		$result=$this->query($sql);
		return $result->rowCount();
	
	}
	public function select($table,$condition){
		$condition=$condition->toArray();
		foreach($condition as $key=>$val){
			$conditionArr[$key]=$val;
		}
		foreach($condition['params'] as $key=>$val){
			// $condition['condition']=str_replace($key,$val,$condition['condition']);
			$condition['condition']=preg_replace("/{$key}/",$val,$condition['condition'],1);
		}
		$condition['condition']=$condition['condition']?" where ".$condition['condition']:"";
		$limit  = $condition['limit'] == '' ? ''  : ' limit ' . $condition['limit'];
		$order  = $condition['order'] == '' ? ''  : ' order by ' . $condition['order'];
		$group  = $condition['group'] == '' ? ''  : ' group by ' . $condition['group'];
		$sql = 'select ' . $condition['select'] . ' from ' . $table ." ". $condition['join'] ." ".$condition['condition'] ." ".$group." ".$order ." ". $limit;
		return $this -> fetchOne($sql);
	}
}
?>