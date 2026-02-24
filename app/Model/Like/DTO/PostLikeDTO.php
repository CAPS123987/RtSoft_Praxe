<?php

namespace App\Model\Like\DTO;

use App\Model\Generics\DTO\DTO;
use App\Model\Like\Repo\PostLikeRepository;
use Nette\Utils\DateTime;

class PostLikeDTO implements DTO
{
    private function __construct(
        public readonly ?int      $id,
        public readonly int       $post_id,
        public readonly int       $user_id,
        public readonly ?DateTime $created_at = null,
    ) {
    }

    public static function create(?int $id, int $post_id, int $user_id, ?DateTime $created_at = null): self
    {
        return new self($id, $post_id, $user_id, $created_at);
    }

    /**
     * @param array<string,mixed> $data
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        /** @var int|null $id */
        $id = $data[PostLikeRepository::ID_COL] ?? null;

        /** @var int $postId */
        $postId = $data[PostLikeRepository::POST_ID_COL];

        /** @var int $userId */
        $userId = $data[PostLikeRepository::USER_ID_COL];

        /** @var string|null $createdAt */
        $createdAt = $data[PostLikeRepository::CREATED_AT_COL] ?? null;

        return new self(
            id: $id,
            post_id: $postId,
            user_id: $userId,
            created_at: $createdAt ? new DateTime($createdAt) : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            PostLikeRepository::ID_COL => $this->id,
            PostLikeRepository::POST_ID_COL => $this->post_id,
            PostLikeRepository::USER_ID_COL => $this->user_id,
            PostLikeRepository::CREATED_AT_COL => $this->created_at,
        ];
    }
}

