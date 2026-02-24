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
        public readonly int      $owner_id = -1,
        public readonly string   $name = '',
        public readonly string   $email = '',
        public readonly string   $content = '',
    ) {
    }

    public static function create(?int $id, int $post_id, int $owner_id, string $name, string $email, string $content, ?DateTime $created_at): self {
        return new self($id, $created_at, $post_id, $owner_id, $name, $email, $content);
    }

    /**
     * @param array<string,Mixed> $data
     * @return self
     * @throws \DateMalformedStringException
     */
    public static function createFromArray(array $data): self
    {
        /** @var int|null $id */
        $id = $data[CommentRepository::ID_COL] ?? null;

        /** @var string|null $createdAt */
        $createdAt = $data[CommentRepository::CREATED_AT_COL] ?? null;

        /** @var int $postId */
        $postId = $data[CommentRepository::POST_ID_COL] ?? -1;

        /** @var int $ownerId */
        $ownerId = $data[CommentRepository::OWNER_COL] ?? -1;

        /** @var string $name */
        $name = $data[CommentRepository::NAME_COL] ?? '';

        /** @var string $email */
        $email = $data[CommentRepository::EMAIL_COL] ?? '';

        /** @var string $content */
        $content = $data[CommentRepository::CONTENT_COL] ?? '';

        return new self(
            id: $id,
            created_at: $createdAt ? new DateTime($createdAt) : null,
            post_id: $postId,
            owner_id: $ownerId,
            name: $name,
            email: $email,
            content: $content,
        );
    }

    public function toArray(): array
    {
        return [
            CommentRepository::ID_COL => $this->id,
            CommentRepository::POST_ID_COL => $this->post_id,
            CommentRepository::OWNER_COL => $this->owner_id,
            CommentRepository::NAME_COL => $this->name,
            CommentRepository::EMAIL_COL => $this->email,
            CommentRepository::CONTENT_COL => $this->content,
            CommentRepository::CREATED_AT_COL => $this->created_at,
        ];
    }
}