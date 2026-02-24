<?php

namespace App\Model\Permission;

class PermissionList
{
    public const string ADD_POST = 'addPost';
    public const string ADD_COMMENT = 'addComment';
    public const string ADD_USER = 'addUser';
    public const string ADD_ADMIN = 'addAdmin';
    public const string ADD_ROLE = 'addRole';
    public const string ADMIN_PANEL = 'adminPanel';

    public const string EDIT_USER = 'editUser';
    public const string EDIT_PERMISSION = 'editPermission';
    public const string EDIT_OWN_POST = 'editOwnPost';
    public const string EDIT_OWN_COMMENT = 'editOwnComment';
    public const string EDIT_ALL_POST = 'editAllPost';
    public const string EDIT_ALL_COMMENT = 'editAllComment';

    public const string DELETE_OWN_POST = 'deleteOwnPost';
    public const string DELETE_OWN_COMMENT = 'deleteOwnComment';
    public const string DELETE_ALL_POST = 'deleteAllPost';
    public const string DELETE_ALL_COMMENT = 'deleteAllComment';


    public string $addPost = self::ADD_POST;
    public string $addComment = self::ADD_COMMENT;
    public string $addUser = self::ADD_USER;
    public string $addAdmin = self::ADD_ADMIN;
    public string $addRole = self::ADD_ROLE;

    public string $adminPanel = self::ADMIN_PANEL;

    public string $editUser = self::EDIT_USER;
    public string $editPermission = self::EDIT_PERMISSION;
    public string $editOwnPost = self::EDIT_OWN_POST;
    public string $editOwnComment = self::EDIT_OWN_COMMENT;
    public string $editAllPost = self::EDIT_ALL_POST;
    public string $editAllComment = self::EDIT_ALL_COMMENT;

    public string $deleteOwnPost = self::DELETE_OWN_POST;
    public string $deleteOwnComment = self::DELETE_OWN_COMMENT;
    public string $deleteAllPost = self::DELETE_ALL_POST;
    public string $deleteAllComment = self::DELETE_ALL_COMMENT;

    /**
     * @var array<string>
     */
    public array $permissions = [
        self::EDIT_USER,
        self::EDIT_PERMISSION,
        self::EDIT_OWN_POST,
        self::EDIT_OWN_COMMENT,
        self::EDIT_ALL_POST,
        self::EDIT_ALL_COMMENT,
        self::DELETE_OWN_POST,
        self::DELETE_OWN_COMMENT,
        self::DELETE_ALL_POST,
        self::DELETE_ALL_COMMENT,
        self::ADMIN_PANEL,
        self::ADD_USER,
        self::ADD_POST,
        self::ADD_COMMENT,
        self::ADD_ADMIN,
        self::ADD_ROLE,
    ];
}