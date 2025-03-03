<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use App\Controllers\PostController;
use App\Controllers\CommentController;

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

// Handle both JSON and form data
$data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $data = $_POST;
    }
}

$userController = new UserController();
$postController = new PostController();
$commentController = new CommentController();

$response = match($action) {
    'getUsers' => $userController->getUsers(),
    'getPosts' => $postController->getPosts(),
    'getComments' => $commentController->getComments(),
    'getCommentsCount' => $commentController->getCommentsCount(),
    'getUser' => $id ? $userController->getUser($id) : ['error' => 'ID is required', 'code' => 400],
    'getPost' => $id ? $postController->getPost($id) : ['error' => 'ID is required', 'code' => 400],
    'getUserPosts' => $id ? $postController->getPostsByUser($id) : ['error' => 'ID is required', 'code' => 400],
    'getPostComments' => $id ? $commentController->getCommentsByPost($id) : ['error' => 'ID is required', 'code' => 400],
    
    'createUser' => $_SERVER['REQUEST_METHOD'] === 'POST' ? $userController->createUser($data) : ['error' => 'Method not allowed', 'code' => 405],
    'createPost' => $_SERVER['REQUEST_METHOD'] === 'POST' ? $postController->createPost($data) : ['error' => 'Method not allowed', 'code' => 405],
    'createComment' => $_SERVER['REQUEST_METHOD'] === 'POST' ? $commentController->createComment($data) : ['error' => 'Method not allowed', 'code' => 405],
    
    'deleteUser' => $_SERVER['REQUEST_METHOD'] === 'DELETE' && $id ? $userController->deleteUser($id) : ['error' => 'Method not allowed or ID missing', 'code' => 405],
    'deletePost' => $_SERVER['REQUEST_METHOD'] === 'DELETE' && $id ? $postController->deletePost($id) : ['error' => 'Method not allowed or ID missing', 'code' => 405],
    'deleteComment' => $_SERVER['REQUEST_METHOD'] === 'DELETE' && $id ? $commentController->deleteComment($id) : ['error' => 'Method not allowed or ID missing', 'code' => 405],
    
    default => ['error' => 'Action not found', 'code' => 404]
};

http_response_code($response['code'] ?? 200);
echo json_encode($response); 