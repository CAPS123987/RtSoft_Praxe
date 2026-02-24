<?php

namespace App\Module\Front\Components\CommentForm;

use App\Model\Comment\Facades\CommentFacade;
use App\Model\Comment\Mapper\CommentMapper;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;

class CommentFormComponent extends Control
{
    public function __construct(
        private readonly CommentFacade $commentFacade,
        private readonly CommentMapper $commentMapper,
        private readonly int $postId,
    )
    {
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/CommentFormComponent.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;

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
    public function commentFormSucceeded(Form $form, array $data): void
    {
        $presenter = $this->getPresenter();
        $user = $presenter->getUser();

        $data["post_id"] = $this->postId;

        if (empty($user->getIdentity())) {
            $presenter->flashMessage('Nejsi přihlášen', 'error');
            $presenter->redirect('this');
        }

        $data["name"] = $user->getIdentity()->getData()["name"];
        $data["owner_id"] = $user->getIdentity()->getId();

        $commentDTO = $this->commentMapper->mapArrayToDTO($data);

        $this->commentFacade->insertDTO($commentDTO);

        $presenter->flashMessage('Děkuji za komentář', 'success');
        $presenter->redirect('this');
    }
}

