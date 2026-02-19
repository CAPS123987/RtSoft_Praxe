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
        public readonly string   $content = '',
    ) {
    }

    public static function create(?int $id, string $title, string $content, ?DateTime $created_at): self
    {
        return new self($id, $created_at, $title, $content);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     * @throws \DateMalformedStringException
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data[PostRepository::ID_COL] ?? null,
            created_at: isset($data[PostRepository::CREATED_AT_COL]) ? new DateTime($data[PostRepository::CREATED_AT_COL]) : null,
            title: $data[PostRepository::TITLE_COL] ?? '',
            content: $data[PostRepository::CONTENT_COL] ?? '',
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