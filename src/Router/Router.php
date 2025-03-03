<?php

namespace App\Router;

use App\Controllers\UserController;
use App\Controllers\PostController;
use App\Controllers\CommentController;

class Router {
    private array $routes = [];
    private UserController $userController;
    private PostController $postController;
    private CommentController $commentController;

    public function __construct() {
        $this->userController = new UserController();
        $this->postController = new PostController();
        $this->commentController = new CommentController();
        $this->registerRoutes();
    }

    private function registerRoutes(): void {
        $this->routes['getUsers'] = [
            'method' => 'GET',
            'handler' => [$this->userController, 'getUsers']
        ];
        $this->routes['getUser'] = [
            'method' => 'GET',
            'handler' => [$this->userController, 'getUser'],
            'params' => ['id']
        ];
        $this->routes['createUser'] = [
            'method' => 'POST',
            'handler' => [$this->userController, 'createUser'],
            'params' => ['name', 'email', 'password']
        ];

        $this->routes['getPosts'] = [
            'method' => 'GET',
            'handler' => [$this->postController, 'getPosts']
        ];
        $this->routes['getPost'] = [
            'method' => 'GET',
            'handler' => [$this->postController, 'getPost'],
            'params' => ['id']
        ];
        $this->routes['getUserPosts'] = [
            'method' => 'GET',
            'handler' => [$this->postController, 'getUserPosts'],
            'params' => ['id']
        ];
        $this->routes['createPost'] = [
            'method' => 'POST',
            'handler' => [$this->postController, 'createPost'],
            'params' => ['title', 'content', 'user_id']
        ];

        $this->routes['getComments'] = [
            'method' => 'GET',
            'handler' => [$this->commentController, 'getComments']
        ];
        $this->routes['getCommentsCount'] = [
            'method' => 'GET',
            'handler' => [$this->commentController, 'getCommentsCount']
        ];
        $this->routes['getPostComments'] = [
            'method' => 'GET',
            'handler' => [$this->commentController, 'getPostComments'],
            'params' => ['id']
        ];
        $this->routes['createComment'] = [
            'method' => 'POST',
            'handler' => [$this->commentController, 'createComment'],
            'params' => ['content', 'user_id', 'post_id']
        ];
    }

    public function dispatch(string $action, array $params = []): array {
        if (!isset($this->routes[$action])) {
            return [
                'status' => 'error',
                'message' => 'Invalid action',
                'code' => 404
            ];
        }

        $route = $this->routes[$action];
        
        if ($route['method'] !== $_SERVER['REQUEST_METHOD']) {
            return [
                'status' => 'error',
                'message' => 'Method not allowed',
                'code' => 405
            ];
        }

        if (isset($route['params'])) {
            $missingParams = [];
            foreach ($route['params'] as $param) {
                if (!isset($params[$param])) {
                    $missingParams[] = $param;
                }
            }
            if (!empty($missingParams)) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required parameters: ' . implode(', ', $missingParams),
                    'code' => 400
                ];
            }
        }

        if ($route['method'] === 'POST' || $route['method'] === 'PUT') {
            $data = [];
            if (isset($route['params'])) {
                foreach ($route['params'] as $param) {
                    $data[$param] = $params[$param];
                }
            }
            $handlerParams = [$data];
        } else {
            $handlerParams = [];
            if (isset($route['params'])) {
                foreach ($route['params'] as $param) {
                    if ($param === 'id' || str_ends_with($param, '_id')) {
                        $handlerParams[] = (int)$params[$param];
                    } else {
                        $handlerParams[] = $params[$param];
                    }
                }
            }
        }

        return call_user_func_array($route['handler'], $handlerParams);
    }
} 