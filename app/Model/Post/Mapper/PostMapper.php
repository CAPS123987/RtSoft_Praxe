<?php
namespace App\Model\Post\Mapper;

use App\Model\Generics\Mapper\Mapper;
use App\Model\Post\DTO\PostDTO;
use App\Model\Post\Repo\PostRepository;
use Nette;

/**
 * @extends Mapper<PostDTO>
 */
final class PostMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : PostDTO
    {
        return PostDTO::create(
            id: $row->{PostRepository::ID_COL},
            title: $row->{PostRepository::TITLE_COL},
            content: $row->{PostRepository::CONTENT_COL},
            owner: $row->{PostRepository::OWNER_COL},
            created_at: $row->{PostRepository::CREATED_AT_COL},
            image: $row->{PostRepository::IMAGE_COL},
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

    /**
     * @param array<string,Mixed> $data
     * @return PostDTO
     */
    public function mapArrayToDTO(array $data): PostDTO
    {
        return PostDTO::createFromArray($data);
    }
}
