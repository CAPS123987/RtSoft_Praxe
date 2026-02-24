<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model;
use App\Model\Like\Facades\PostLikeFacade;
use App\Model\Permission\PermissionList;
use App\Module\Front\Components\CommentForm\CommentFormComponent;
use App\Module\Front\Components\CommentForm\CommentFormComponentFactory;
use Nette;

final class PostPresenter extends BasePresenter
{
    public function __construct(
        private Model\Post\Facades\PostCommentFacade $postCommentFacade,
        private Model\Post\Facades\PostFacade        $postFacade,
        private CommentFormComponentFactory           $commentFormComponentFactory,
        private PostLikeFacade                        $postLikeFacade,
        private PermissionList $perms,
    ) {
        parent::__construct($perms);
    }

    public function renderShow(int $id): void
    {
        $post=null;
        try {
            $post = $this->postFacade->getDTOById($id);
        } catch (\RuntimeException $e) {
            $this->error('Stránka nebyla nalezena');
        }

        $this->template->post = $post;
        $this->template->comments = $this->postCommentFacade->getPostsComments($post);
        $this->template->likeCount = $this->postLikeFacade->getLikeCount($id);
        $this->template->hasLiked = $this->getUser()->isLoggedIn()
            ? $this->postLikeFacade->hasUserLiked($id, $this->getUser()->getId())
            : false;
    }

    public function handleToggleLike(int $id): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            $this->sendJson(['error' => 'Musíte být přihlášen.']);
            return;
        }

        $userId = $this->getUser()->getId();
        $liked = $this->postLikeFacade->toggleLike($id, $userId);
        $count = $this->postLikeFacade->getLikeCount($id);

        $this->sendJson([
            'liked' => $liked,
            'likeCount' => $count,
        ]);
    }

    protected function createComponentCommentForm(): CommentFormComponent
    {
        $id = (int) $this->getParameter('id');
        return $this->commentFormComponentFactory->create($id);
    }

}