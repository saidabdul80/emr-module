<?php

	class Model {		
		private $dbCon;
		public function __construct(){
			$this->dbCon = $GLOBALS['adodb']['db'];
		}

		public static function get($table,$data, $all = true){
            $self = new self;
			return $self->fetch($table,$data, $all);
		}

		public static function find($table,$id, $arr =[]){
			$sql = "SELECT * FROM $table WHERE id=?";
            $self = new self;
			$records = $self->dbCon->ExecuteNoLog($sql, [$id]);			
            if($records){
                return $records->fields;        
            }
            return [];
		}
        
		
		private function fetch($table,$arr = [], $all){
			if($arr == []){ $all = true;}
			$where_clause = '';
			$vals = [];
			foreach ($arr as $key => $value) {
                if($where_clause == ''){
                    $where_clause .= " $key = ?  ";
                }else{
                    $where_clause .= " AND $key = ?  ";
                }			   
                $vals = [$value];
			}
            if (!empty($vals)) {                
                $sql= "SELECT * FROM $table WHERE  $where_clause";                
            }else{
                $sql= "SELECT * FROM $table";
            }   
    
			$recordset = $this->dbCon->ExecuteNoLog($sql, $vals);
            $list = [];
            if($recordset){
                while ($record = sqlFetchArray($recordset)) {
                    $list[] = $record;
                }            
                if($all){
                    return json_decode(json_encode($list));
                }
                return json_decode(json_encode($list))[0];
            }
            return [];
		}

        public static function insert($statement, $binds = array())
        {
            // Below line is to avoid a nasty bug in windows.
            if (empty($binds)) {
                $binds = false;
            }
    
            //Run a adodb execute
            // Note the auditSQLEvent function is embedded in the
            //   Execute function.
            $recordset = $GLOBALS['adodb']['db']->Execute($statement, $binds, true);
            if ($recordset === false) {
                throw new SqlQueryException($statement, "Insert failed. SQL error " . getSqlLastError() . " Query: " . $statement);
            }
    
            // Return the correct last id generated using function
            //   that is safe with the audit engine.
            return $GLOBALS['lastidado'] > 0 ? $GLOBALS['lastidado'] : $GLOBALS['adodb']['db']->Insert_ID();
        }

        public static function update($statement, $binds = array()){
            // Below line is to avoid a nasty bug in windows.
            if (empty($binds)) {
                $binds = false;
            }
    
            //Run a adodb execute
            // Note the auditSQLEvent function is embedded in the
            //   Execute function.
            $recordset = $GLOBALS['adodb']['db']->Execute($statement, $binds, true);
            if ($recordset === false) {
                return "Update failed";
            }
            return 'Updated Successfully';                        
        }
	} // class ends here
    
?>

