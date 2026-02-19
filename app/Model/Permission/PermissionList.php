<?php

namespace App\Model\Permission;

class PermissionList
{
    public const string ADD_POST = 'addPost';
    public const string EDIT_POST = 'editPost';
    public const string DELETE_POST = 'deletePost';
    public const string ADD_COMMENT = 'addComment';
    public const string EDIT_COMMENT = 'editComment';
    public const string DELETE_COMMENT = 'deleteComment';
    public const string ADMIN_PANEL = 'adminPanel';
    public const string EDIT_PERMISSION = 'editPermission';
    public const string ADD_USER = 'addUser';

    public string $addPost = self::ADD_POST;
    public string $editPost = self::EDIT_POST;
    public string $deletePost = self::DELETE_POST;
    public string $addComment = self::ADD_COMMENT;
    public string $editComment = self::EDIT_COMMENT;
    public string $deleteComment = self::DELETE_COMMENT;
    public string $adminPanel = self::ADMIN_PANEL;
    public string $editPermission = self::EDIT_PERMISSION;
    public string $addUser = self::ADD_USER;

    public array $permissions = [
        self::ADD_POST,
        self::EDIT_POST,
        self::DELETE_POST,
        self::ADD_COMMENT,
        self::EDIT_COMMENT,
        self::DELETE_COMMENT,
        self::ADMIN_PANEL,
        self::EDIT_PERMISSION,
        self::ADD_USER,
    ];
}