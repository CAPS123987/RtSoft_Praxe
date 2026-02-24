<?php

namespace App\Module\Front\Components\CommentForm;

use App\Model\Comment\Facades\CommentFacade;
use App\Model\Comment\Mapper\CommentMapper;

class CommentFormComponentFactory
{
    public function __construct(
        private readonly CommentFacade $commentFacade,
        private readonly CommentMapper $commentMapper,
    )
    {
    }

    public function create(int $postId): CommentFormComponent
    {
        return new CommentFormComponent(
            $this->commentFacade,
            $this->commentMapper,
            $postId,
        );
    }
}

