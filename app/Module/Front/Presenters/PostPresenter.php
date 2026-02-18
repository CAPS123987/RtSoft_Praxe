<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use App\Model;
use Nette;
use Nette\Application\UI\Form;

final class PostPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Model\Post\Facades\PostCommentFacade $postCommentFacade,
        private Model\Post\Facades\PostFacade        $postFacade,
        private Model\Comment\Facades\CommentFacade  $commentFacade,
        private Model\Comment\Mapper\CommentMapper   $commentMapper,
    ) {
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
    }

    protected function createComponentCommentForm(): Form
    {
        $form = new Form; // means Nette\Application\UI\Form

        $form->addText('name', 'Jméno:')
            ->setRequired();

        $form->addEmail('email', 'E-mail:');

        $form->addTextArea('content', 'Komentář:')
            ->setRequired();

        $form->addSubmit('send', 'Publikovat komentář');

        $form->onSuccess[] = [$this, 'commentFormSucceeded'];

        return $form;
    }

    /**
     * @param array<string,string> $data
     */
    public function commentFormSucceeded(array $data): void
    {
        $id = $this->getParameter('id');
        $data["post_id"] = $id;

        $commentDTO = $this->commentMapper->mapArrayToDTO($data);

        $this->commentFacade->insertDTO($commentDTO);

        $this->flashMessage('Děkuji za komentář', 'success');
        $this->redirect('this');
    }

}