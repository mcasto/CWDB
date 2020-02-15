<?php
    // if db copy exists, delete it
    $fn = __DIR__ . "/example.sqlite";
    if(file_exists($fn)){
        unlink($fn);
    }

    // create copy of database to work with so that, no matter how many times you run this script, the database will have everything the script needs for its examples
    copy(__DIR__ . "/example_clean.sqlite", $fn);

    require_once(__DIR__ . "/database.class.php");
    $db = new database;     // Using DEFAULT_DB from db_config.inc.php
?>

<!-- NOTE: I know this HTML/CSS is ugly, but the point was to showcase the database class not my HTML/CSS skills. I may make it prettier over time, but, for now, this is adequate. -->

<style>
    table{
        border-collapse: collapse;
        margin-bottom: 1rem;
    }

    thead{
        background-color: darkgray;
        color: white;
    }

    th, td{
        padding: 1rem;
        text-align: center;
        border: 1px solid black;
    }
</style>

<!-- listTables() example -->
<table>
    <thead>
        <tr>
            <th>
                Table List (<?= $db->listTables()->count(); ?>)     <!-- count() example -->
            </th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($db->listTables()->results as $tblName): ?>
        <tr>
            <td><?= $tblName; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- listFields() example -->
<?php foreach($db->listTables()->results as $tblName): ?>
    <table>
        <thead>
            <tr>
                <th>
                    Table Fields in <?= $tblName; ?> (<?= $db->listFields($tblName)->count(); ?>)
                </th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($db->listFields($tblName)->results as $fldName): ?>
            <tr>
                <td><?= $fldName; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>

<!-- execute() example -->
<?php
    // get random record from dummy_blog
    $r = $db->query("SELECT * FROM dummy_blog")->random();      // demonstration of random() function

    // display this record
    echo "<hr><h3>Before <i>post_title</i> update</h3><pre>";
    print_r($r);
    echo "</pre><hr>";

    // update the post_title for that record -- used named token to demonstrate it
    $db->execute("UPDATE dummy_blog SET post_title = 'THIS IS A TEST' WHERE id = :id", [
        "id"=>$r['id']
    ]);

    // display updated record -- used wildcard to demonstrate it
    echo "<h3>After <i>post_title</i> update</h3><pre>";
    print_r($db->query("SELECT * FROM dummy_blog WHERE id = ?", [
        $r['id']
    ])->first());       // demonstration of first() function
    echo "</pre><hr>";
?>

<!-- last() example -->
<?php
    // get last record from mockaroo table
    echo "<pre>";
    print_r($db->query("SELECT * FROM mockaroo")->last());
    echo "</pre><hr>";
?>

<!-- itemExists() example -->
<?php
    // look for a record that *does* exist in mockaroo table
    $itmExists = $db->itemExists("mockaroo", "id", 86);
    echo "Item with ID 86 exists in mockaroo table: ";
    echo ($itmExists) ? "true" : "false";
    echo "<hr>";
?>

<!-- delete() example -->
<?php
    echo "Delete item with ID 86 from mockaroo table<hr>";

    // delete the record we previously checked with itemExists()
    $db->delete("mockaroo", "id", 86);

    // illustrate that it no longer exists
    $itmExists = $db->itemExists("mockaroo", "id", 86);
    echo "Item with ID 86 exists in mockaroo table: ";
    echo ($itmExists) ? "true" : "false";
    echo "<hr>";
?>

<!-- insert() example -->
<?php
    // designate unique id for record (I personally prefer to assign my own row id)
    $id = uniqid();

    echo "Insert new record with ID: " . $id . "<hr>Display new record:<br>";
    // insert a record into dummy_blog table
    $db->insert("dummy_blog", [
        "id"=>$id,
        "post_title"=>"INSERT TEST",
        "post_body"=>"This is a test of the insert functionality.",
        "author_email"=>"dummy@blog.com",
        "posted_on"=>date("Y-m-d")
    ]);

    // display new record
    echo "<pre>";
    print_r($db->query("SELECT * FROM dummy_blog WHERE id = ?", [
        $id
    ])->last());    // since there's only one record in result set, I could use first() or last()
    echo "</pre><hr>";
?>

<!-- update() example -->
<?php
    echo "Update previously inserted record:<br>";
    
    // update the previously inserted record
    $db->update("dummy_blog", [
        "post_title"=>"UPDATE TEST",
        "post_body"=>"I just updated this record."
    ], "id", $id);

    // display updated record
    echo "<pre>";
    print_r($db->query("SELECT * FROM dummy_blog WHERE id = ?", [
        $id
    ])->first());    // since there's only one record in result set, I could use first() or last()
    echo "</pre><hr>";
?>