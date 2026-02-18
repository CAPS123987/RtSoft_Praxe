<?php
namespace App\Model\Comment\Mapper;

use App\Model\Comment\DTO\CommentDTO;
use App\Model\Generics\Mapper\Mapper;
use App\Model\Post\DTO\PostDTO;
use Nette;

/**
 * @extends Mapper<CommentDTO>
 */
final class CommentMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : CommentDTO
    {
        return CommentDTO::create(
            id: $row->id,
            post_id: $row->post_id,
            name: $row->name,
            email: $row->email,
            content: $row->content,
            created_at: $row->created_at
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<PostDTO>
     */
    public function mapAll(Nette\Database\Table\Selection $selection): array
    {
        $result = [];
        foreach ($selection as $row) {
            $result[] = $this->map($row);
        }
        return $result;
    }

    public function mapArrayToDTO(array $data): CommentDTO
    {
        return CommentDTO::createFromArray($data);
    }
}
