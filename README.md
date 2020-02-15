# CWDB
 My database abstraction class.

# History
Back in ~2005, I was doing freelance web application development. I put together the predecessor of this class to save myself some typing.

Since then, I have revised the code to take advantage of new methodologies like PDO.

Recently, I discovered [UserSpice](https://github.com/mudmin/UserSpice4), and it inspired me to make some more changes modeled on their database class.

# Notes
All of my projects to date have used either MySQL or SQLite databases. As such, this class currently only has methods to deal with those databases. I may modify it over time to handle others such as PostgreSQL, but that is a low priority for me.

# Install
Modify db_config.inc.php to suit your needs. Include or require database.class.php & instantiate it. On instantiation, it will use the DEFAULT_DB set in confir, or you can call it with a database name, which will override the default database set in db_config.

# Methods
1. listTables()
    * This returns an instance of the database class with the list of table names in the results array
2. listFields($tblName)
    * This returns an instance of the database class with the list of field names for the specified table in the results array
3. itemExists($idField, $idVal)
    * Returns true if a record with $idVal in $idField exists
4. delete($tblName, $idField, $idVal)
    * Deletes the record from $tblName where $idField = $idVal
5. query($sqlPrep, $valList = null)
    * If $valList is null, this pulls the results of the raw $sql into the result set
    * If $valList is an array, $sqlPrep should contain wildcards or named tokens, and $valList should be formatted accordingly as an array of values for the wildcards or an associative array with key names that the token list.

    * This method returns an instance of the entire database class, which allows you to do things like:
    
    `$firstRec = $db->query("SELECT * FROM table")->first()`

6. execute($sqlPrep, $valList)
    * You can use this to perform any SQL command, including insert, delete, and update, but it is primarily used internally and for execution of commands beyond insert, delete, and update.
7. insert($tblName, $valList)
    * $valList = an associative array in the form of [
        "FIELD_NAME"=>"VALUE",
        ...
    ]
    * It inserts the data into the specified table.
8. update($tblName, $valList, $idField, $idVal)
    * This updates the record in $tblName, identified by $idField = $idVal, and updates the fields with the values in $valList, which is formatted in the same way as in the insert method.
9. delete($tblName, $idField, $idVal)
    * Deletes the record, identified by $idField = $idVal, from $tblName
10. results
    * This is an array that holds the results of queries
11. first()
    * Returns the first record of $db->results
12. last()
    * Returns the last record of $db->results
13. count()
    * Returns the number of records in $db->results