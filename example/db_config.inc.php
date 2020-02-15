<?php
  //DB Type
  define("DB_TYPE", "sqlite");

  //Default database - OPTIONAL (for SQLite, use the relative/path/to/file)
  define("DEFAULT_DB", __DIR__ . "/example.sqlite");
  
  /*
    You can set the database when you instantiate the database object.
    $db = new database([db_name]);
    This will also override DEFAULT_DB if it is set.
  */


// Note: I could delete all of these since they're not required for sqlite, but it doesn't hurt anything to leave them here either.

  //DB server location
  define("DB_SERVER", "");

  //DB port
  define("DB_PORT", "");

  //DB charset
  define("DB_CHARSET", "");   // defalts to utf8 if this is empty string

  //DB username
  define("DB_USER", "");

  //DB password
  define("DB_PASS", "");  
?>