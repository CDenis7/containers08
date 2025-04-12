<?php
require_once __DIR__ . '/modules/database.php';
require_once __DIR__ . '/modules/page.php';
require_once __DIR__ . '/config.php';

$db = new Database($config["db"]["path"]);

$page = new Page(__DIR__ . '/templates/index.tpl');

// Get page ID, with proper input validation
$pageId = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Make sure page ID is valid
if ($pageId < 1) {
    $pageId = 1;
}

$data = $db->Read("page", $pageId);

// If page not found, display a default page
if (!$data) {
    $data = [
        'title' => 'Page Not Found',
        'content' => 'The requested page does not exist.'
    ];
}

echo $page->Render($data);