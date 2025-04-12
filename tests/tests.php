<?php

require_once __DIR__ . '/testframework.php';

$config = [
    "db" => [
        "path" => "/var/www/db/db.sqlite"
    ]
];

require_once '/var/www/html/modules/database.php';
require_once '/var/www/html/modules/page.php';

$tests = new TestFramework();

// test 1: check database connection
function testDbConnection() {
    global $config;
    $db = new Database($config["db"]["path"]);
    return assertExpression(true, "Database connection successful", "Failed to connect to database");
}

// test 2: test count method
function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count("page");
    return assertExpression($count == 3, "Count method returned correct value: $count", "Count method failed, expected 3 but got $count");
}

// test 3: test create method
function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $initialCount = $db->Count("page");
    
    $newId = $db->Create("page", [
        "title" => "Test Page",
        "content" => "Test Content"
    ]);
    
    $newCount = $db->Count("page");
    $record = $db->Read("page", $newId);
    
    $db->Delete("page", $newId); // Clean up
    
    return assertExpression(
        $newCount == $initialCount + 1 && $record["title"] == "Test Page" && $record["content"] == "Test Content",
        "Create method created a record successfully",
        "Create method failed"
    );
}

// test 4: test read method
function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $record = $db->Read("page", 1);
    
    return assertExpression(
        $record && $record["id"] == 1 && $record["title"] == "Page 1" && $record["content"] == "Content 1",
        "Read method returned correct record",
        "Read method failed"
    );
}

// test 5: test update method
function testDbUpdate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $newId = $db->Create("page", [
        "title" => "Update Test",
        "content" => "Before Update"
    ]);
    
    $db->Update("page", $newId, [
        "title" => "Updated Title",
        "content" => "After Update"
    ]);
    
    $record = $db->Read("page", $newId);
    
    $db->Delete("page", $newId); // Clean up
    
    return assertExpression(
        $record["title"] == "Updated Title" && $record["content"] == "After Update",
        "Update method updated the record successfully",
        "Update method failed"
    );
}

// test 6: test delete method
function testDbDelete() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $newId = $db->Create("page", [
        "title" => "Delete Test",
        "content" => "To Be Deleted"
    ]);
    
    $initialCount = $db->Count("page");
    $db->Delete("page", $newId);
    $newCount = $db->Count("page");
    
    return assertExpression(
        $newCount == $initialCount - 1,
        "Delete method deleted the record successfully",
        "Delete method failed"
    );
}

// test 7: test fetch method
function testDbFetch() {
    global $config;
    $db = new Database($config["db"]["path"]);
    
    $results = $db->Fetch("SELECT * FROM page WHERE id <= 3 ORDER BY id");
    
    return assertExpression(
        count($results) == 3 && 
        $results[0]["id"] == 1 && 
        $results[1]["id"] == 2 && 
        $results[2]["id"] == 3,
        "Fetch method returned correct results",
        "Fetch method failed"
    );
}

// test 8: test page rendering
function testPageRender() {
   $page = new Page('/var/www/html/templates/index.tpl');
    
    $data = [
        "title" => "Test Title",
        "content" => "Test Content"
    ];
    
    $rendered = $page->Render($data);
    
    return assertExpression(
        strpos($rendered, "Test Title") !== false && 
        strpos($rendered, "Test Content") !== false,
        "Page rendering works correctly",
        "Page rendering failed"
    );
}

// add tests
$tests->add('Database connection', 'testDbConnection');
$tests->add('Table count', 'testDbCount');
$tests->add('Data create', 'testDbCreate');
$tests->add('Data read', 'testDbRead');
$tests->add('Data update', 'testDbUpdate');
$tests->add('Data delete', 'testDbDelete');
$tests->add('Data fetch', 'testDbFetch');
$tests->add('Page render', 'testPageRender');

// run tests
$tests->run();

echo "Test results: " . $tests->getResult();
