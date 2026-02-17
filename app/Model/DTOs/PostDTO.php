<?php

namespace App\Model\DTOs;

use Nette;
use Nette\Utils\DateTime;

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

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }
}