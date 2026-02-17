<?php

declare(strict_types=1);

namespace App\Module\Front\Presenters;

use Nette;
use Nette\Application\UI\Form;
use App\Model;

final class PostPresenter extends Nette\Application\UI\Presenter
{
    public function __construct(
        private Model\Facades\PostCommentFacade $postCommentFacade,
        private Model\Facades\PostFacade $postFacade,
        private Model\Facades\CommentFacade $commentFacade,
        private Model\Mapper\Mapper $mapper,
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

        $commentDTO = $this->mapper->mapArrayToDTO($data, Model\DTOs\CommentDTO::class);

        $this->commentFacade->insertDTO($commentDTO);

        $this->flashMessage('Děkuji za komentář', 'success');
        $this->redirect('this');
    }

}