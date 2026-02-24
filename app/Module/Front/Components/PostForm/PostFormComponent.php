<?php

namespace App\Module\Front\Components\PostForm;

use App\Model\Image\ImageUploadFacade;
use App\Model\Permission\PermissionList;
use App\Model\Post\Facades\PostFacade;
use App\Model\Post\Mapper\PostMapper;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;

class PostFormComponent extends Control
{
    public function __construct(
        private readonly PostFacade $postFacade,
        private readonly PostMapper $postMapper,
        private readonly PermissionList $perms,
        private readonly ImageUploadFacade $imageUploadFacade,
        private readonly ?int $postId = null,
    )
    {
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/PostFormComponent.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
    {
        $form = new Form;
        $form->addText('title', 'Titulek:')
            ->setRequired();
        $form->addUpload('postImage', 'Obrázek:')
            ->addRule($form::Image, 'Obrázek musí být JPEG, PNG, GIF, WebP or AVIF.')
            ->addRule($form::MaxFileSize, 'Maximální velikost je 10 MB.', 1024 * 1024 * 10);
        $form->addTextArea('content', 'Obsah:')
            ->setRequired();

        $form->addSubmit('send', 'Uložit a publikovat');
        $form->onSuccess[] = [$this, 'postFormSucceeded'];

        if ($this->postId !== null) {
            $post = $this->postFacade->getDTOById($this->postId);
            $form->setDefaults($post->toArray());
        }

        return $form;
    }

    /**
     * @param array<string,string> $data
     */
    public function postFormSucceeded(Form $form, array $data): void
    {
        $presenter = $this->getPresenter();

        if (!$presenter->isAllowed($this->perms->addPost)) {
            $presenter->flashMessage('Nemáte oprávnění přidat tento příspěvek.', 'error');
            $presenter->redirect('Homepage:');
        }

        $data['id'] = $this->postId;
        $data['owner_id'] = $presenter->getUser()->getIdentity()->getId();

        /** @var FileUpload $postImage */
        $postImage = $data['postImage'];
        unset($data['postImage']);

        $imagePath = $this->imageUploadFacade->upload($postImage, 'posts');

        if ($imagePath !== null) {
            // Nový obrázek nahrán — smazat starý, pokud existoval
            if ($this->postId !== null) {
                $existingPost = $this->postFacade->getDTOById($this->postId);
                if ($existingPost->image !== null) {
                    $this->imageUploadFacade->delete($existingPost->image);
                }
            }
            $data['image'] = $imagePath;
        } elseif ($this->postId !== null) {
            // Žádný nový obrázek — zachovat původní
            $existingPost = $this->postFacade->getDTOById($this->postId);
            $data['image'] = $existingPost->image;
        }

        $id = $this->postFacade->saveDTO($this->postMapper->mapArrayToDTO($data));

        $presenter->flashMessage('Příspěvek byl úspěšně publikován.', 'success');
        $presenter->redirect('Post:show', strval($id));
    }
}

