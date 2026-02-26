<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model;
use App\Model\Like\Facades\CommentLikeFacade;
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
        private CommentLikeFacade                     $commentLikeFacade,
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

        // Comment likes – pro každý komentář počet liků a zda uživatel likoval
        $commentLikeCounts = [];
        $commentHasLiked = [];
        foreach ($this->template->comments as $comment) {
            $commentId = $comment->id;
            $commentLikeCounts[$commentId] = $this->commentLikeFacade->getLikeCount($commentId);
            $commentHasLiked[$commentId] = $this->getUser()->isLoggedIn()
                ? $this->commentLikeFacade->hasUserLiked($commentId, $this->getUser()->getId())
                : false;
        }
        $this->template->commentLikeCounts = $commentLikeCounts;
        $this->template->commentHasLiked = $commentHasLiked;
    }

    public function handleToggleLike(int $id): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            if ($this->isAjax()) {
                $this->flashMessage('Pro lajkování se musíte přihlásit.', 'error');
                $this->redrawControl('flashes');
            }
            return;
        }

        $userId = $this->getUser()->getId();
        $this->postLikeFacade->toggleLike($id, $userId);

        if ($this->isAjax()) {
            $this->redrawControl('like');
        } else {
            $this->redirect('this');
        }
    }

    public function handleToggleCommentLike(int $commentId): void
    {
        if (!$this->getUser()->isLoggedIn()) {
            if ($this->isAjax()) {
                $this->flashMessage('Pro lajkování se musíte přihlásit.', 'error');
                $this->redrawControl('flashes');
            }
            return;
        }

        $userId = $this->getUser()->getId();
        $this->commentLikeFacade->toggleLike($commentId, $userId);

        if ($this->isAjax()) {
            $this->redrawControl('comments');
        } else {
            $this->redirect('this');
        }
    }

    protected function createComponentCommentForm(): CommentFormComponent
    {
        $id = (int) $this->getParameter('id');
        return $this->commentFormComponentFactory->create($id);
    }

}