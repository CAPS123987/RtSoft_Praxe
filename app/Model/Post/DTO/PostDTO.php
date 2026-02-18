<?php

namespace App\Model\Post\DTO;

use App\Model\Generics\DTO\DTO;
use Nette\Utils\DateTime;
use App\Model\Post\Repo\PostRepository;

class PostDTO implements DTO
{
    private function __construct(
        public readonly ?int      $id,
        public readonly string   $title = '',
        public readonly string   $content = '',
        public readonly ?DateTime $created_at,
    ) {
    }

    public static function create(?int $id, string $title, string $content, ?DateTime $created_at): self
    {
        return new self($id, $title, $content, $created_at);
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data[PostRepository::ID_COL] ?? null,
            title: $data[PostRepository::TITLE_COL] ?? '',
            content: $data[PostRepository::CONTENT_COL] ?? '',
            created_at: isset($data[PostRepository::CREATED_AT_COL]) ? new DateTime($data[PostRepository::CREATED_AT_COL]) : null
        );
    }

    public function toArray(): array
    {
        return [
            PostRepository::ID_COL => $this->id,
            PostRepository::TITLE_COL => $this->title,
            PostRepository::CONTENT_COL => $this->content,
            PostRepository::CREATED_AT_COL => $this->created_at,
        ];
    }
}