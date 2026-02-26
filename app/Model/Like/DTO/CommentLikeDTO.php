<?php

namespace App\Model\Like\DTO;

use App\Model\Generics\DTO\DTO;
use App\Model\Like\Repo\CommentLikeRepository;

class CommentLikeDTO implements DTO
{
    private function __construct(
        public readonly ?int $id,
        public readonly int  $comment_id,
        public readonly int  $user_id,
    ) {
    }

    public static function create(?int $id, int $comment_id, int $user_id): self
    {
        return new self($id, $comment_id, $user_id);
    }

    /**
     * @param array<string,mixed> $data
     * @return self
     */
    public static function createFromArray(array $data): self
    {
        /** @var int|null $id */
        $id = $data[CommentLikeRepository::ID_COL] ?? null;

        /** @var int $commentId */
        $commentId = $data[CommentLikeRepository::COMMENT_ID_COL];

        /** @var int $userId */
        $userId = $data[CommentLikeRepository::USER_ID_COL];

        return new self(
            id: $id,
            comment_id: $commentId,
            user_id: $userId,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            CommentLikeRepository::ID_COL => $this->id,
            CommentLikeRepository::COMMENT_ID_COL => $this->comment_id,
            CommentLikeRepository::USER_ID_COL => $this->user_id,
        ];
    }
}

