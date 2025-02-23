<?php

namespace App\Permissions;

use App\Models\User;

final class Permissions
{
    public const UpdateOwnProfile = 'profile:own:update';
    public const CRUDOwnPost = 'post:own:crud';
    public const CRUDAnyPost = 'post:any:crud';
    public const CRUDOwnComment = 'comment:own:crud';
    public const CRUDAnyComment = 'comment:any:crud';
    public const CRUDAnyCategory = 'category:any:crud';

    public static function adminPermissions()
    {
        return array_merge(self::userPermissions(), [
            self::CRUDAnyPost,
            self::CRUDAnyComment,
            self::CRUDAnyCategory,
        ]);
    }
    public static function userPermissions()
    {
        return [
            self::CRUDOwnPost,
            self::CRUDOwnComment,
            self::UpdateOwnProfile,
        ];
    }
}
