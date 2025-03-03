<?php

namespace App\Controllers;

use App\Models\Post;
use App\Helpers\Response;
use App\Requests\PostRequest;
use App\Actions\Post\GetAllPostsAction;
use App\Actions\Post\GetPostByIdAction;
use App\Actions\Post\GetPostsByUserIdAction;
use App\Actions\Post\CreatePostAction;
use App\Actions\Post\UpdatePostAction;
use App\Actions\Post\DeletePostAction;

class PostController {
    private Post $post;
    private GetAllPostsAction $getAllPosts;
    private GetPostByIdAction $getPostById;
    private GetPostsByUserIdAction $getPostsByUserId;
    private CreatePostAction $createPost;
    private UpdatePostAction $updatePost;
    private DeletePostAction $deletePost;

    public function __construct() {
        $this->post = new Post();
        $this->getAllPosts = new GetAllPostsAction();
        $this->getPostById = new GetPostByIdAction();
        $this->getPostsByUserId = new GetPostsByUserIdAction();
        $this->createPost = new CreatePostAction();
        $this->updatePost = new UpdatePostAction();
        $this->deletePost = new DeletePostAction();
    }

    public function getPosts(): array {
        try {
            $posts = $this->getAllPosts->execute();
            return Response::success($posts);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getPost(int $id): array {
        try {
            $post = $this->getPostById->execute($id);
            
            if (!$post) {
                return Response::error('Post not found', 404);
            }

            return Response::success($post);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function getPostsByUser(int $userId): array {
        try {
            $posts = $this->getPostsByUserId->execute($userId);
            return Response::success($posts);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function createPost(array $data): array {
        $request = new PostRequest($data);
        if (!$request->validate()) {
            return Response::error('Validation failed', 422, $request->getErrors());
        }

        try {
            $id = $this->createPost->execute($request->getData());
            return Response::success(['id' => $id], 'Post created successfully');
        } catch (\Exception $e) {

            $code = $e->getCode();
            $code = is_int($code) && $code > 0 ? $code : 500;
            return Response::error($e->getMessage(), $code, $request->getErrors());
        }
    }

    public function updatePost(int $id, array $data): array {
        $request = (new PostRequest($data))->forUpdate();
        if (!$request->validate()) {
            return Response::error('Validation failed', 422, $request->getErrors());
        }

        try {
            $success = $this->updatePost->execute($id, $request->getData());
            return $success 
                ? Response::success(null, 'Post updated successfully')
                : Response::error('Failed to update post', 500);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $code = is_int($code) && $code > 0 ? $code : 500;
            return Response::error($e->getMessage(), $code, $request->getErrors());
        }
    }

    public function deletePost(int $id): array {
        try {
            $success = $this->deletePost->execute($id);
            return $success 
                ? Response::success(null, 'Post deleted successfully')
                : Response::error('Failed to delete post', 500);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $code = is_int($code) && $code > 0 ? $code : 500;
            return Response::error($e->getMessage(), $code);
        }
    }
} 