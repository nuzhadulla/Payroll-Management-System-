<?PHP
	/**
	* MMOTOP - Shopping Cart
	* 
	* PHP version 5
	*
	* class DB
	* Common Database Class
	* This file contains the commonly used Includes, Functions to be used for MMOTOP
	* @project MMOTOP
	* @version 1.0
	* @copyright  	2006-2010 Oclock Software Pvt Ltd
	*	
	* @package MMOTOP
	* @subpackage Database
	* @filesource
	*/
	class classDB {
		/* 	
		USAGE:
		# 1. Set using extend class
		require ("name_of_db_extend_class.php");
		$objDB = new extendClassName();
		# 2. Set object properties directly
		$objDB = new classDB();
		$objDB->db	ion($Server, $Database, $Username, $Password);
		# Set the SQL Query 
		$objDB->dbSetQuery("SQL QUERY","select");   //supports: "select", "insert", "update", "delete"
		# Run Query
		$objDB->dbExecuteQuery();
		# To get the resulset 
		$resultset = $objDB->dbSelectQuery();
		# Additional Information
		#  for : insert, update, detete    this will give last insert_id
		$objDB->dbLastInsertRow 
		# for: select   this will return Number of Rows from the resultset
		$numrows = $objDB->dbQueryNumRows;   		
		*/		
		var $dbLinkSet;
		var $dbLink;
		var $dbServer;
		var $dbDatabase;
		var $dbUser;
		var $dbPass;
		var $dbType;		
		var $dbConnected;
		var $dbQuery;
		var $dbQuerySet;
		var $dbQueryType;
		var $dbQueryTypeSet;
		var $dbResult;
		var $dbQueryNumRows;
		var $dbResultSetInMem;
		var $dbLastInsertRow;
		var $update_char_rev;
		var $update_char_where_pos;
		var $update_char_sql_length;
		var $update_char_cut;
		var $update_char_end_sql;
		function classDB() {
			$this->dbServer = "";
			$this->dbDatabase = "";
			$this->dbUser = "";
			$this->dbPass = "";
			$this->dbType = 1;		
			$this->dbLinkSet = 1;
			$this->dbQuery = "";
			$this->dbQuerySet = 0;
			$this->dbQueryType = "";
			$this->dbQueryTypeSet = 0;
			$this->dbQueryNumRows = 0;		
			$this->dbResultSetInMem = 0;
			$this->update_char_rev = "";
			$this->update_char_where_pos = 0;
			$this->update_char_sql_length = 0;
			$this->update_char_cut = "";
			$this->update_char_end_sql = "";
			$this->dbConnected = 0;			
		}
		function doPrint($str) {
			if($_SERVER['REMOTE_ADDR']==$strSecureTestingIP)
				print $str;
		}
		function dbConnect() {
			// only connect once.
			global $_REQUEST;
			$this->dbConnected 	= $_REQUEST["DB_CONNECTED"];
			$this->dbLink		= $_REQUEST["DB_LINK"];		
			if ($this->dbConnected==0) {
				$this->dbLink = mysqli_connect($this->dbServer, $this->dbUser, $this->dbPass) or $this->reportError("Could not connect to Server.");
				//$this->doPrint("CONNECTING DATABASE : LINK => ".$this->dbLink);
				$_REQUEST["DB_CONNECTED"] 	= $this->dbConnected = 1;
				$_REQUEST["DB_LINK"] 		= $this->dbLink;		
				mysqli_select_db ($this->dbLink,$this->dbDatabase) or $this->reportError("Could not select Database.");	
			} 
		}
		function dbDisconnect() {
			// mysql_close is not necessary, non-persistent links are automatically closed at script end.
			// no effect anyway in Linux.		
			if ($this->dbConnected == 1) {
				 $this->doPrint("CLOSING DATABASE");
				 mysqli_close($this->dbLink);
				 $this->dbConnected = 0;
			} else {
				$this->dbError(2);
			}
		} 
		function dbRunQuery() {
			global $global_log;
			if ($this->dbConnected == 1) {
				if ($this->dbQuerySet == 1) {				
					if ($this->dbQueryType == "insert") {
						// replace ampersands unless part of an existing character entity
						$this->dbQuery = preg_replace("/&(?!amp;)(?!lt;)(?!gt;)(?!#*;)/", "&amp;", $this->dbQuery);
						// replace all instances of < > with respective html entities
						$this->dbQuery = preg_replace(array("/</","/>/"), array("&#60;","&#62;"), $this->dbQuery);
					} elseif ($this->dbQueryType == "update") {				
						// Reverse string to find first occurence of WHERE - PHP doesnt at this time
						// support reverse word matching
						$this->update_char_rev = strrev($this->dbQuery);
						$this->update_char_where_pos = strpos($this->update_char_rev, "EREHW");					
						// Character length of Query
						$this->update_char_sql_length = strlen($this->dbQuery);					
						// return query before WHERE clause
						$this->update_char_cut = substr($this->dbQuery, 0, (($this->update_char_sql_length - $this->update_char_where_pos) -5));
						// grab end of query from WHERE clause
						$this->update_char_end_sql = substr($this->dbQuery, (($this->update_char_sql_length - $this->update_char_where_pos) -5));
						// replace all instances of & with &amp; unless lookforward regex finds
						// &amp; &lt; &gt; &#*;
						$this->update_char_cut = preg_replace("/&(?!amp;)(?!lt;)(?!gt;)(?!#*;)/", "&amp;", $this->update_char_cut);
						// replace all instances of < > with respective html entities
						//$this->update_char_cut = preg_replace(array("/</","/>/"), array("&#60;","&#62;"), $this->update_char_cut);
						// put revised first part of query and WHERE clause back together
						$this->dbQuery = $this->update_char_cut . $this->update_char_end_sql;
					}
//					print $this->dbQuery."<br>";
					switch($this->dbType) {
						case 1:
							$this->dbQueryResult = mysqli_query ($this->dbLink, $this->dbQuery) or $this->reportError();
							$global_log[] = $this->dbQuery;						
							break;	
						case 2:
							$this->dbQueryResult = mssql_query ($this->dbQuery, $this->dbLink) or $this->reportError("Error in SQL Query.");
							$global_log[] = $this->dbQuery;
							break;
					}					
					// if no results returned, dont try to work with query result.
					if ($this->dbQueryResult<>"") {
						 $this->dbResultSetInMem = 1;
						if ($this->dbQueryTypeSet == 1) {
							if ($this->dbQueryType == "select") {
								switch($this->dbType) {
									case 1:
										$this->dbQueryNumRows = mysqli_num_rows($this->dbQueryResult) or $this->dbQueryNumRows = 0;									
										break;
									case 2:
										$this->dbQueryNumRows = mssql_num_rows($this->dbQueryResult) or $this->dbQueryNumRows = 0;
										break;
								}								
							} elseif (($this->dbQueryType == "insert") || ($this->dbQueryType == "update") || ($this->dbQueryType == "delete")) {
								switch ($this->dbType) {
									case 1:
										$this->dbQueryNumRows = mysqli_affected_rows($this->dbLink) or $this->dbQueryNumRows = 0;
										$this->dbLastInsertRow = mysqli_insert_id($this->dbLink);
										break;
									case 2:
										$this->dbQueryNumRows = mssql_fetch_row(mssql_query("SELECT @@ROWCOUNT")) or $this->dbQueryNumRows = 0;
										$row = mssql_fetch_row(mssql_query("SELECT @@IDENTITY"));
										$this->dbLastInsertRow = $row[0];
										break;
								}								
							} elseif ($this->dbQueryType == "create"){
								$this->dbQueryNumRows = 1;
							} elseif ($this->dbQueryType == "lock"){
								$this->dbQueryNumRows = 1;
							} elseif ($this->dbQueryType == "unlock"){
								$this->dbQueryNumRows = 1;
							} else {
								$this->dbQueryNumRows = 0;
							}
						}
					}
					else {
						$this->dbQueryNumRows = 0;
					}
				}
			} else {
				$this->dbError(3);
			}
		}
		function dbConnection($Server, $Database, $Username, $Password) {
			if ($Server != "" && $Database != "" && $Username != "" && $Password != "") {
				$this->dbServer = $Server;
				$this->dbDatabase = $Database;
				$this->dbUser = $Username;
				$this->dbPass = $Password;				
				$this->dbLinkSet = 1;
			} else {
				$this->dbLinkSet = 0;	
				$this->dbError(4);
			}
		}
		function dbSetQuery($query_string,$query_type="select") {
			if ($query_string != "") {
				$this->dbQuery = $query_string;			
				$this->dbQuerySet = 1;
			} else {
				$this->dbError(5);
				$this->dbQuerySet = 0;
			}		
			if ($query_type == "select") {
				$this->dbQueryType = $query_type;
				$this->dbQueryTypeSet = 1;
			} elseif ($query_type == "insert" || $query_type == "update" || $query_type == "delete" || $query_type == "drop") {
				$this->dbQueryType = $query_type;
				$this->dbQueryTypeSet = 1;
			} elseif ($query_type == "create") {
				$this->dbQueryType = $query_type;
				$this->dbQueryTypeSet = 1;
			} else {
				if ($query_type == "") {
					$this->dbError(6);
				}
				$this->dbQueryTypeSet = 0;
			}	
		}
		function dbFreeResultSet() {
			if ($this->dbResultSetInMem == 1) {
				switch ($this->dbType) {
					case 1:
						mysqli_free_result($this->dbQueryResult);
						break;	
					case 2:
						mssqli_free_result($this->dbQueryResult);
						break;
				}				
			} else {
			 // nothing
			}
		}
		function dbSelectQuery() {
			$this->dbConnect();
			$this->dbRunQuery();
			if ($this->dbQueryNumRows > 0) {
			   $row = array(); 
			   $record = array();
			   switch ($this->dbType) {
					case 1:
						while ($row = mysqli_fetch_array($this->dbQueryResult)) {
							$record[] = $row; 				
						}	
						break;	
					case 2:
						while ($row = mssql_fetch_array($this->dbQueryResult)) {
							$record[] = $row; 				
						}	
						break;
				}			   
				return $record;	
			} else {
				return false;
			}
		}
		function dbExecuteQuery() {
			$this->dbConnect();
			$this->dbRunQuery();
		}
		function dbError($error_no) {
			switch ($error_no) {
				case 1:
					$error_desc = "Unknow Error occured.";
					break;
				case 2:
					$error_desc = "Connection does not exists, could not disconnect from DB.";
					break;
				case 3:
					$error_desc = "Disconnected from DB.";
					break;
				case 4:
					$error_desc = "Could not connect with DB. One of the value(s) is not supplied 1. Server 2. DBName 3. DBUser 4. DBPassword.";
					break;
				case 5:
					$error_desc = "QueryString is not supplied.";
					break;
				case 6:
					$error_desc = "QueryType is not supplied.";
					break;
			}		
			$this->reportError($error_desc);
		}
		function reportError($error = "") { // Sends Error Report 1. To logfile 2. To Email 3. system logs
			print $error;
			global $config;
			$log_file_name = date("M_Y")."_logs.txt";
			$log_time = date("D d-M-Y H:i:s");
			$log_string = "[".$log_time."] ".$error;
			print mysqli_error($this->dbLink)."<br>CHECK THE Query => <br>".$this->dbQuery;
			switch ($global_config["error_report"]) {
				case 1:	// reportError in logfile			
					echo $this->dbQuery."<br>".mysqli_error();
					$fp = fopen($global_config["path"]."logs\\$log_file_name",'a+');
					fputs($fp,$log_string."\r\n");
					break;
				case 2: // reportError via Email
					include_once($global_config["path"]."includes\\libmail.php");
					$FromEmail = $global_config["from_email"];
					$ToEmail = $global_config["support_email"];
					$Subject = "Error Report";
					$MailContent = $log_string;
					$m = new Mail; 
					$m->From($FromEmail);
					$m->To($ToEmail);
					$m->Subject($Subject);
					$m->Body($MailContent);	
					$m->Priority(3) ;	
					$m->Send();
					break;

				case 3:	// reportError in system logs
					error_log($log_string);
					break;		
			}
			if($global_config["debug_mode"]) {
				switch ($this->dbType) {
					case 1:
						$error_string = "<b>Error: </b><i>".$error."</i><br><b>Reason: </b>".mysqli_error();		
						break;	
					case 2:
						$error_string = "<b>Error: </b><i>".$error."</i><br>";
						break;
				}
				print $error_string;
			}			
			exit(0);
		}		
	}	
?>