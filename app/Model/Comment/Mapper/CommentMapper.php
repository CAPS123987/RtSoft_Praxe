<?php
namespace App\Model\Comment\Mapper;

use App\Model\Comment\DTO\CommentDTO;
use App\Model\Generics\Mapper\Mapper;
use App\Model\Comment\Repo\CommentRepository;
use Nette;

/**
 * @extends Mapper<CommentDTO>
 */
final class CommentMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : CommentDTO
    {
        return CommentDTO::create(
            id: $row->{CommentRepository::ID_COL},
            post_id: $row->{CommentRepository::POST_ID_COL},
            owner_id: $row->{CommentRepository::OWNER_COL},
            name: $row->{CommentRepository::NAME_COL},
            email: $row->{CommentRepository::EMAIL_COL},
            content: $row->{CommentRepository::CONTENT_COL},
            created_at: $row->{CommentRepository::CREATED_AT_COL}
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<CommentDTO>
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
     * @param array<string,Mixed> $data
     * @return CommentDTO
     */
    public function mapArrayToDTO(array $data): CommentDTO
    {
        return CommentDTO::createFromArray($data);
    }
}
