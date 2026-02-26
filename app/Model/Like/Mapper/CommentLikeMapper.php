<?php

namespace App\Model\Like\Mapper;

use App\Model\Generics\Mapper\Mapper;
use App\Model\Like\DTO\CommentLikeDTO;
use App\Model\Like\Repo\CommentLikeRepository;
use Nette;

/**
 * @extends Mapper<CommentLikeDTO>
 */
final class CommentLikeMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row): CommentLikeDTO
    {
        return CommentLikeDTO::create(
            id: $row->{CommentLikeRepository::ID_COL},
            comment_id: $row->{CommentLikeRepository::COMMENT_ID_COL},
            user_id: $row->{CommentLikeRepository::USER_ID_COL},
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<CommentLikeDTO>
     */
    public function mapAll(Nette\Database\Table\Selection $selection): array
    {
        $result = [];
        foreach ($selection as $row) {
            $result[] = $this->map($row);
        }
        return $result;
    }

    /**
     * @param array<string,mixed> $data
     * @return CommentLikeDTO
     */
    public function mapArrayToDTO(array $data): CommentLikeDTO
    {
        return CommentLikeDTO::createFromArray($data);
    }
}

