<?php

namespace App\Model\Mapper;

use App\Model\DTOs\CommentDTO;
use App\Model\DTOs\DTO;
use App\Model\DTOs\PostDTO;
use App\Model\Repos\CommentRepository;
use Nette;
final class Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : DTO
    {
        $rowArray = $row->toArray();

        return match ($row->getTable()->getName()) {
            'posts' => $this->mapPost($row),
            'comments' => $this->mapComment($row),
            default => null,
        };
    }

    public function mapAll(Nette\Database\Table\Selection $selection): array
    {
        $result = [];
        foreach ($selection as $row) {
            $result[] = $this->map($row);
        }
        return $result;
    }

    private function mapPost(Nette\Database\Table\ActiveRow $row): PostDTO
    {
        return PostDTO::create(
            id: $row->id,
            title: $row->title,
            content: $row->content,
            created_at: $row->created_at
        );
    }

    public function mapComment(Nette\Database\Table\ActiveRow $row): CommentDTO
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

    public function mapArrayToDTO(array $data, string $class): DTO|null
    {
        return match ($class) {
            PostDTO::class => PostDTO::create(
                id: $data['id'] ?? null,
                title: $data['title'] ?? '',
                content: $data['content'] ?? '',
                created_at: $data['created_at'] ?? null
            ),
            CommentDTO::class => CommentDTO::create(
                id: $data['id'] ?? null,
                post_id: $data['post_id'] ?? null,
                name: $data['name'] ?? '',
                email: $data['email'] ?? '',
                content: $data['content'] ?? '',
                created_at: $data['created_at'] ?? null
            ),
            default => null,
        };
    }
}