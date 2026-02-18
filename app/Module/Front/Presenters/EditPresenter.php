<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class EditPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Model\Post\Facades\PostFacade               $postFacade,
        private Model\Comment\Facades\CommentFacade         $commentFacade,
        private Model\Comment\Facades\CommentDeletionFacade $commentDeletionFacade,
        private Model\Post\Facades\PostDeletionFacade       $postDeletionFacade,
        private Model\Post\Mapper\PostMapper                $postMapper,
    ) {
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
        $id = $this->getParameter('id');

        $data['id'] = $id;

        $id = $this->postFacade->saveDTO($this->postMapper->mapArrayToDTO($data));


        $this->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $this->redirect('Post:show', strval($id));
    }
}