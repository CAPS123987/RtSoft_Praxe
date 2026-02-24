<?php

namespace App\Model\Like\Mapper;

use App\Model\Generics\Mapper\Mapper;
use App\Model\Like\DTO\PostLikeDTO;
use App\Model\Like\Repo\PostLikeRepository;
use Nette;

/**
 * @extends Mapper<PostLikeDTO>
 */
final class PostLikeMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row): PostLikeDTO
    {
        return PostLikeDTO::create(
            id: $row->{PostLikeRepository::ID_COL},
            post_id: $row->{PostLikeRepository::POST_ID_COL},
            user_id: $row->{PostLikeRepository::USER_ID_COL},
            created_at: $row->{PostLikeRepository::CREATED_AT_COL},
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<PostLikeDTO>
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
     * @return PostLikeDTO
     */
    public function mapArrayToDTO(array $data): PostLikeDTO
    {
        return PostLikeDTO::createFromArray($data);
    }
}

