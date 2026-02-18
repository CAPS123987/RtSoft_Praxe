<?php
namespace App\Model\Post\Mapper;

use App\Model\Comment\DTO\CommentDTO;
use App\Model\Generics\Mapper\Mapper;
use App\Model\Post\DTO\PostDTO;
use Nette;

/**
 * @extends Mapper<PostDTO>
 */
final class PostMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : PostDTO
    {
        return PostDTO::create(
            id: $row->id,
            title: $row->title,
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

    public function mapArrayToDTO(array $data): PostDTO
    {
        return PostDTO::createFromArray($data);
    }
}
