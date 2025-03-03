<?php

namespace App\Controllers;

use App\Models\Comment;
use App\Helpers\Response;
use App\Requests\CommentRequest;
use App\Actions\Comment\GetAllCommentsAction;
use App\Actions\Comment\GetCommentByIdAction;
use App\Actions\Comment\GetCommentsByPostIdAction;
use App\Actions\Comment\GetCommentsByUserIdAction;
use App\Actions\Comment\CreateCommentAction;
use App\Actions\Comment\UpdateCommentAction;
use App\Actions\Comment\GetCommentsCountAction;
use App\Actions\Comment\DeleteCommentAction;

class CommentController {
    private Comment $comment;
    private GetAllCommentsAction $getAllComments;
    private GetCommentByIdAction $getCommentById;
    private GetCommentsByPostIdAction $getCommentsByPostId;
    private GetCommentsByUserIdAction $getCommentsByUserId;
    private CreateCommentAction $createComment;
    private UpdateCommentAction $updateComment;
    private GetCommentsCountAction $getCommentsCount;
    private DeleteCommentAction $deleteComment;

    public function __construct() {
        $this->comment = new Comment();
        $this->getAllComments = new GetAllCommentsAction();
        $this->getCommentById = new GetCommentByIdAction();
        $this->getCommentsByPostId = new GetCommentsByPostIdAction();
        $this->getCommentsByUserId = new GetCommentsByUserIdAction();
        $this->createComment = new CreateCommentAction();
        $this->updateComment = new UpdateCommentAction();
        $this->getCommentsCount = new GetCommentsCountAction();
        $this->deleteComment = new DeleteCommentAction();
    }

    private function execute(callable $action): array {
        try {
            return Response::success($action());
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getComments(): array {
        return $this->execute(fn() => $this->getAllComments->execute());
    }

    public function getComment(int $id): array {
        return $this->execute(function() use ($id) {
            $comment = $this->getCommentById->execute($id);
            if (!$comment) {
                throw new \Exception('Comment not found', 404);
            }
            return $comment;
        });
    }

    public function getCommentsByPost(int $postId): array {
        return $this->execute(fn() => $this->getCommentsByPostId->execute($postId));
    }

    public function getCommentsByUser(int $userId): array {
        return $this->execute(fn() => $this->getCommentsByUserId->execute($userId));
    }

    public function createComment(array $data): array {
        $request = new CommentRequest($data);
        if (!$request->validate()) {
            return Response::error('Validation failed', 422, $request->getErrors());
        }

        return $this->execute(function() use ($request) {
            $id = $this->createComment->execute($request->getData());
            return ['id' => $id, 'message' => 'Comment created successfully'];
        });
    }

    public function updateComment(int $id, array $data): array {
        $request = (new CommentRequest($data))->forUpdate();
        if (!$request->validate()) {
            return Response::error('Validation failed', 422, $request->getErrors());
        }

        return $this->execute(function() use ($id, $request) {
            $success = $this->updateComment->execute($id, $request->getData());
            return ['success' => $success, 'message' => 'Comment updated successfully'];
        });
    }

    public function deleteComment(int $id): array {
        return $this->execute(function() use ($id) {
            $success = $this->deleteComment->execute($id);
            return ['success' => $success, 'message' => 'Comment deleted successfully'];
        });
    }

    public function getCommentsCount(): array {
        return $this->execute(fn() => $this->getCommentsCount->execute());
    }
} 