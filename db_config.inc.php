<?php
  //DB Type
  define("DB_TYPE", "mysql");

  //DB server location
  define("DB_SERVER", "127.0.0.1");

  //DB port
  define("DB_PORT", "");

  //DB charset
  define("DB_CHARSET", "");   // defalts to utf8 if this is empty string

  //DB username
  define("DB_USER", "root");

  //DB password
  define("DB_PASS", "root");

  //Default database - OPTIONAL (for SQLite, use the relative/path/to/file)
  define("DEFAULT_DB", "pagination_magic");

  /*
		You can set the database when you instantiate the database object.
		$db = new database([db_name]);
		This will also override DEFAULT_DB if it is set.
	*/
?>