<?php

namespace App\Model\DTOs;

use Nette;
use Nette\Utils\DateTime;

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