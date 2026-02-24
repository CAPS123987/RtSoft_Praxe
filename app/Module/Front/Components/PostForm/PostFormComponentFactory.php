<?php

namespace App\Module\Front\Components\PostForm;

use App\Model\Image\ImageUploadFacade;
use App\Model\Permission\PermissionList;
use App\Model\Post\Facades\PostFacade;
use App\Model\Post\Mapper\PostMapper;

class PostFormComponentFactory
{
    public function __construct(
        private readonly PostFacade $postFacade,
        private readonly PostMapper $postMapper,
        private readonly PermissionList $perms,
        private readonly ImageUploadFacade $imageUploadFacade,
    )
    {
    }

    public function create(?int $postId = null): PostFormComponent
    {
        return new PostFormComponent(
            $this->postFacade,
            $this->postMapper,
            $this->perms,
            $this->imageUploadFacade,
            $postId,
        );
    }
}

