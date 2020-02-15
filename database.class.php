<?php 	
    set_error_handler("handleErrors", E_STRICT);
	
	function handleErrors($eNum, $eStr){
		die("Error: " . $eNum . "\n\n" . $eStr);
	}
	
	include_once(__DIR__ . "/db_config.inc.php");

	class database{
		function __construct($db = ""){
			$db = ($db == "") ? DEFAULT_DB : $db;
			$charset = (DB_CHARSET == "") ? "utf8" : DB_CHARSET;
			
			$this->dbType = DB_TYPE;

			switch(strtolower(DB_TYPE)){
				case "mariadb":
				case "mysql":
					$dsn = "mysql:host=" . DB_SERVER . ";dbname=" . $db . ";charset=" . $charset . ";";
					if(DB_PORT != "") $dsn .= "port=" . DB_PORT . ";";

					try{
						$this->pdo = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
					} catch(PDOException $e){
						die("Unable to connect to DB.");
					}
					break;
					
				case "sqlite":
					$dsn = "sqlite:" . $db;

					try{
						$this->pdo = new PDO($dsn);
					} catch(PDOException $e){
						die("Unable to connect to DB.");
					}
					break;
			}

			$this->results = [];
			$this->insertID = false;
		}

		function listTables(){		// list tables in database
			if($this->dbType == "sqlite"){
				// get table list
				$rs = $this->query("SELECT * FROM sqlite_master WHERE type='table'")->results;

				// set results array to list of table namse
				$this->results = array_column($rs, "name");
			} else {
				// get table list
				$tableList = $this->query("SHOW TABLES")->results;

				// get column key
				$k = array_keys($tableList[0])[0];

				// set results array to list of table names
				$this->results = array_column($tableList, $k);
			}

			return $this;
		}

		function listFields($tableName) {		// list fields in given table
			$fld = array();

			if($this->dbType == "sqlite"){
				// get fields in table
				$this->results = array_column($this->query("PRAGMA table_info('" . $tableName . "')")->results, "name");
			}

			if($this->dbType == "mysql"){
				// get fields in table
				$this->results = array_column($this->query("show columns from " . $tableName)->results, "Field");
			}

			return $this;
		}

		function execute($sqlPrep, $valList = null){	// execute arbitrary SQL with a prepared statement
			$this->sql = $sqlPrep;
			$this->valList = $valList;

			try{
				$prep = $this->pdo->prepare($sqlPrep);
			} catch(PDOException $e) {
				error_log("Unable to prepare query -- #001");
				die();
			}

			try{
				$prep->execute($valList);
			} catch(PDOException $e) {
				error_log("Unable to execute query -- #002");
				die();
			}

			return $this;
		}

		function query($sqlPrep, $valList = null){		// pull data from database using a prepared statement
			try{
				$prep = $this->pdo->prepare($sqlPrep);
			} catch(PDOException $e) {
				error_log("Unable to prepare query -- #003");
				die();
			}

			try{
				$prep->execute($valList);
			} catch(PDOException $e) {
				error_log("Unable to execute query -- #004");
				die();
			}

			try{
				$this->results = $prep->fetchAll(PDO::FETCH_ASSOC);
			} catch(PDOException $e) {
				die("Unable to fetch results -- #005");
			}

			$this->insertID = false;	// insertID only valid after insert command

			return $this;
		}

		function first(){				// return first record of results
			return $this->results[0];
		}

		// return last record of results
		function last(){
			$tmp = $this->results;		// copy results array
			return array_pop($tmp);
		}

		// return the number of records in results
		function count(){
			return count($this->results);
		}

		// return random record from results
		function random(){
			$tmp = $this->results;		// copy results array
			shuffle($tmp);
			return array_shift($tmp);
		}
		
		function insert($tblName, $valList){	// insert record into table
			$fldList = array_keys($valList);
			$numVals = count($valList);
			$placeholderList = [];
			foreach($fldList as $f){
				$placeholderList[] = ":" . $f;
			}
						
			$sql = "INSERT INTO " . $tblName . " (\"" . implode("\",\"", $fldList) . "\") VALUES (" . implode(",", $placeholderList) . ")";

			return $this->execute($sql, $valList);
		}

		function update($tblName, $valList, $idField, $idVal){		// update record in table
			$sql = "UPDATE " . $tblName . " SET \n";
			$uList = [];
			foreach($valList as $k=>$v){
				$uList[] = $k . " = :" . $k;
			}
			$sql .= implode(",\n", $uList);
			$sql .= "\nWHERE " . $idField . " = :" . $idField;
			$valList[$idField] = $idVal;

			return $this->execute($sql, $valList);
		}

		function delete($tblName, $idField, $idVal){		// delete record from table
			// check for existence of record before deletion to prevent error
			if(!$this->itemExists($tblName, $idField, $idVal)) return false;

			$sql = "DELETE FROM " . $tblName . " WHERE " . $idField . " = ?";
			return $this->execute($sql, [ $idVal ]);
		}

		function itemExists($tblName, $idField, $idVal){	// check if item exists
            $sql = "SELECT COUNT(*) AS iCount FROM " . $tblName . " WHERE " . $idField . " = ?";
			return ($this->query($sql, [ $idVal ])->first()['iCount'] > 0);
		}
	}
?>