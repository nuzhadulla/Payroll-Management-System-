<?php
	include_once(MAIN_COMMON_PATH."class.DB.php" );
	class extendsClassDB extends classDB {
		/**
		* Sets the global values for the Database and Initializes the DB
		* @uses classDB() using {@link DB::classDB()}
		*/

		function extendsClassDB() {
			global $global_config;

			$this->classDB();
			$this->dbServer 	= $global_config["DBHost"];
			$this->dbDatabase 	= $global_config["DBDatabaseName"];
			$this->dbUser 		= $global_config["DBUserName"];
			$this->dbPass 		= $global_config["DBPassword"];
			$this->tblPrefix	= $global_config["DBTablePrefix"];
		}
	}

?>