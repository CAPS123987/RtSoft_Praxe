<?php

namespace App\Model\Comment\DTO;

use App\Model\Generics\DTO\DTO;
use Nette\Utils\DateTime;
use App\Model\Comment\Repo\CommentRepository;

class CommentDTO implements DTO
{
    private function __construct(
        public readonly ?int      $id,
        public readonly ?DateTime $created_at,
        public readonly int      $post_id = -1,
        public readonly string   $name = '',
        public readonly string   $email = '',
        public readonly string   $content = '',
    ) {
    }

    public static function create(?int $id, int $post_id, string $name, string $email, string $content, ?DateTime $created_at): self {
        return new self($id, $created_at, $post_id, $name, $email, $content);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     * @throws \DateMalformedStringException
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data[CommentRepository::ID_COL] ?? null,
            created_at: isset($data[CommentRepository::CREATED_AT_COL]) ? new DateTime($data[CommentRepository::CREATED_AT_COL]) : null,
            post_id: $data[CommentRepository::POST_ID_COL] ?? -1,
            name: $data[CommentRepository::NAME_COL] ?? '',
            email: $data[CommentRepository::EMAIL_COL] ?? '',
            content: $data[CommentRepository::CONTENT_COL] ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            CommentRepository::ID_COL => $this->id,
            CommentRepository::POST_ID_COL => $this->post_id,
            CommentRepository::NAME_COL => $this->name,
            CommentRepository::EMAIL_COL => $this->email,
            CommentRepository::CONTENT_COL => $this->content,
            CommentRepository::CREATED_AT_COL => $this->created_at,
        ];
    }
}