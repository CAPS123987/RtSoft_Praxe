<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model;
use App\Model\Permission\PermissionList;
use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends BasePresenter
{
    public function __construct(
        private Model\Post\Facades\PostFacade               $postFacade,
        private Model\Comment\Facades\CommentFacade         $commentFacade,
        private Model\Comment\Facades\CommentDeletionFacade $commentDeletionFacade,
        private Model\Post\Facades\PostDeletionFacade       $postDeletionFacade,
        private Model\Post\Mapper\PostMapper                $postMapper,
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

        $this->getComponent('postForm')
            ->setDefaults($post->toArray());
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


    protected function createComponentPostForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired();
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];

        return $form;
    }

    /**
     * @param array<string,string> $data
     */
    public function postFormSucceeded(array $data): void
    {
        if(!parent::isAllowed($this->perms->addPost)) { 
            $this->flashMessage('Nemáte oprávnění přidat tento příspěvek.', 'error');
            $this->redirect('Homepage:');
        }

        $id = $this->getParameter('id');

        $data['id'] = $id;
        $data['owner_id'] = $this->getUser()->getIdentity()->getId();

        $id = $this->postFacade->saveDTO($this->postMapper->mapArrayToDTO($data));


        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('Post:show', strval($id));
    }
}