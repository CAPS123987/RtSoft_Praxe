<?php

namespace App\Model\Comment\DTO;

use App\Model\Generics\DTO\DTO;
use Nette\Utils\DateTime;
use App\Model\Comment\Repo\CommentRepository;

class CommentDTO implements DTO
{
    private function __construct(
        public readonly ?int      $id,
        public readonly int      $post_id = -1,
        public readonly string   $name = '',
        public readonly string   $email = '',
        public readonly string   $content = '',
        public readonly ?DateTime $created_at,
    ) {
    }

    public static function create(?int $id, int $post_id, string $name, string $email, string $content, ?DateTime $created_at): self {
        return new self($id, $post_id, $name, $email, $content, $created_at);
    }

    public static function createFromArray(array $data): self
    {
        return new self(
            id: $data[CommentRepository::ID_COL] ?? null,
            post_id: $data[CommentRepository::POST_ID_COL] ?? -1,
            name: $data[CommentRepository::NAME_COL] ?? '',
            email: $data[CommentRepository::EMAIL_COL] ?? '',
            content: $data[CommentRepository::CONTENT_COL] ?? '',
            created_at: isset($data[CommentRepository::CREATED_AT_COL]) ? new DateTime($data[CommentRepository::CREATED_AT_COL]) : null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'post_id' => $this->post_id,
            'name' => $this->name,
            'email' => $this->email,
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }
}