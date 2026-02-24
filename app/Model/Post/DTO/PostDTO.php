<?php

namespace App\Model\Post\DTO;

use App\Model\Generics\DTO\DTO;
use Nette\Utils\DateTime;
use App\Model\Post\Repo\PostRepository;

class PostDTO implements DTO
{
    private function __construct(
        public readonly ?int      $id,
        public readonly ?DateTime $created_at,
        public readonly string   $title = '',
        public readonly int      $owner = -1,
        public readonly string   $content = '',
        public readonly ?string  $image = null,
    ) {
    }

    public static function create(?int $id, string $title, string $content, int $owner, ?DateTime $created_at, ?string $image = null): self
    {
        return new self($id, $created_at, $title, $owner, $content, $image);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     * @throws \DateMalformedStringException
     */
    public static function createFromArray(array $data): self
    {
        /** @var int|null $id */
        $id = $data[PostRepository::ID_COL] ?? null;

        /** @var string|null $createdAt */
        $createdAt = $data[PostRepository::CREATED_AT_COL] ?? null;

        /** @var string $title */
        $title = $data[PostRepository::TITLE_COL] ?? '';

        /** @var int $owner */
        $owner = $data[PostRepository::OWNER_COL] ?? -1;

        /** @var string $content */
        $content = $data[PostRepository::CONTENT_COL] ?? '';

        /** @var string|null $image */
        $image = $data[PostRepository::IMAGE_COL] ?? null;

        return new self(
            id: $id,
            created_at: $createdAt ? new DateTime($createdAt) : null,
            title: $title,
            owner: $owner,
            content: $content,
            image: $image,
        );
    }

    public function toArray(): array
    {
        return [
            PostRepository::ID_COL => $this->id,
            PostRepository::TITLE_COL => $this->title,
            PostRepository::CONTENT_COL => $this->content,
            PostRepository::IMAGE_COL => $this->image,
            PostRepository::OWNER_COL => $this->owner,
            PostRepository::CREATED_AT_COL => $this->created_at,
        ];
    }
}