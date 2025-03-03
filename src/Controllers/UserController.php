<?php

namespace App\Controllers;

use App\Models\User;
use App\Helpers\Response;
use App\Requests\UserRequest;
use App\Actions\User\GetAllUsersAction;
use App\Actions\User\GetUserByIdAction;
use App\Actions\User\CreateUserAction;
use App\Actions\User\UpdateUserAction;
use App\Actions\User\DeleteUserAction;
use App\Actions\User\FindUserByEmailAction;

class UserController {
    private User $user;
    private GetAllUsersAction $getAllUsers;
    private GetUserByIdAction $getUserById;
    private CreateUserAction $createUser;
    private UpdateUserAction $updateUser;
    private DeleteUserAction $deleteUser;
    private FindUserByEmailAction $findUserByEmail;

    public function __construct() {
        $this->user = new User();
        $this->getAllUsers = new GetAllUsersAction();
        $this->getUserById = new GetUserByIdAction();
        $this->createUser = new CreateUserAction();
        $this->updateUser = new UpdateUserAction();
        $this->deleteUser = new DeleteUserAction();
        $this->findUserByEmail = new FindUserByEmailAction();
    }

    public function getUsers(): array {
        try {
            $users = $this->getAllUsers->execute();
            return Response::success($users);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function getUser(int $id): array {
        try {
            $user = $this->getUserById->execute($id);
            
            if (!$user) {
                return Response::error('User not found', 404);
            }

            return Response::success($user);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function createUser(array $data): array {
        $request = new UserRequest($data);
        if (!$request->validate()) {
            return Response::error('Validation failed', 422, $request->getErrors());
        }

        try {
            // Check email uniqueness
            if ($this->findUserByEmail->execute($data['email'])) {
                return Response::error('Email already exists', 409);
            }

            $id = $this->createUser->execute($request->getData());
            return Response::success(['id' => $id], 'User created successfully');
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), 500);
        }
    }

    public function updateUser(int $id, array $data): array {
        $request = (new UserRequest($data))->forUpdate();
        if (!$request->validate()) {
            return Response::error('Validation failed', 422, $request->getErrors());
        }

        try {
            $success = $this->updateUser->execute($id, $request->getData());
            return $success 
                ? Response::success(null, 'User updated successfully')
                : Response::error('Failed to update user', 500);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function deleteUser(int $id): array {
        try {
            $success = $this->deleteUser->execute($id);
            return $success 
                ? Response::success(null, 'User deleted successfully')
                : Response::error('Failed to delete user', 500);
        } catch (\Exception $e) {
            return Response::error($e->getMessage(), $e->getCode() ?: 500);
        }
    }
} 