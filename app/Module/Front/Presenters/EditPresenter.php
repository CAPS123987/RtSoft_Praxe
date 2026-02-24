<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model;
use App\Model\Permission\PermissionList;
use App\Module\Front\Components\PostForm\PostFormComponent;
use App\Module\Front\Components\PostForm\PostFormComponentFactory;
use Nette;

final class EditPresenter extends BasePresenter
{
    public function __construct(
        private Model\Post\Facades\PostFacade               $postFacade,
        private Model\Comment\Facades\CommentFacade         $commentFacade,
        private Model\Comment\Facades\CommentDeletionFacade $commentDeletionFacade,
        private Model\Post\Facades\PostDeletionFacade       $postDeletionFacade,
        private PostFormComponentFactory                     $postFormComponentFactory,
        private PermissionList $perms,
    ) {
        parent::__construct($perms);
    }

    public function startup(): void
    {
        parent::startup();

        if (!$this->getUser()->isLoggedIn()) {
            $this->redirect('Sign:in');
        }
    }

    public function renderEdit(int $id): void
    {
        $post = $this->postFacade->getDTOById($id);

        if(!(parent::isAllowedWithOwnerOrAll($this->perms->editAllPost,$this->perms->editOwnPost, $post->owner)))
        {
            $this->flashMessage('Nemáte oprávnění upravovat tento příspěvek.', 'error');
            $this->redirect('Homepage:');
        }
    }

    public function actionDeletePost(int $id): void
    {
        try {
            $post = $this->postFacade->getDTOById($id);
        } catch (\RuntimeException $e) {
            $this->flashMessage('Příspěvek nebyl nalezen.', 'error');
            $this->redirect('Homepage:');
        }

        if(!(parent::isAllowedWithOwnerOrAll($this->perms->deleteAllPost,$this->perms->deleteOwnPost, $post->owner)))
        {
            $this->flashMessage('Nemáte oprávnění smazat tento příspěvek.', 'error');
            $this->redirect('Homepage:');
        }

        if($this->postDeletionFacade->deletePostDTO($post)) {
            $this->flashMessage('Příspěvek byl úspěšně smazán.', 'success');
        } else {
            $this->flashMessage('Při mazání příspěvku došlo k chybě. Zkuste to prosím znovu.', 'error');
        }

        //dump($post);
        $this->redirect('Homepage:');
    }

    public function actionDeleteComment(int $commentId): void
    {
        try {
            $comment = $this->commentFacade->getDTOById($commentId);
        } catch (\RuntimeException $e) {
            $this->flashMessage('Komentář nebyl nalezen.', 'error');
            $this->redirect('Homepage:');
        }

        if(!(parent::isAllowedWithOwnerOrAll($this->perms->deleteAllComment,$this->perms->deleteOwnComment, $comment->owner_id)))
        {
            $this->flashMessage('Nemáte oprávnění smazat tento komentář.', 'error');
            $this->redirect('Homepage:');
        }

        if($this->commentDeletionFacade->deleteCommentDTOTransaction($comment)) {
            $this->flashMessage('Komentář byl úspěšně smazán.', 'success');
        } else {
            $this->flashMessage('Při mazání komentáře došlo k chybě. Zkuste to prosím znovu.', 'error');
        }

        //dump($comment);
        if(!empty($comment->post_id)) {
            $this->redirect('Post:show', ['id' => $comment->post_id]);
        }
        $this->redirect('Homepage:');
    }


    protected function createComponentPostForm(): PostFormComponent
    {
        $id = $this->getParameter('id');
        return $this->postFormComponentFactory->create($id !== null ? (int) $id : null);
    }
}