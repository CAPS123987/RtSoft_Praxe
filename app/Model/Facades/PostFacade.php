<?php

namespace App\Model\Facades;

use App\Model\DTOs\postDTO;
use Nette\Database\Table\ActiveRow;
use App;

/**
 * @extends DTOFacade<PostDTO>
 */
final class PostFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\Repos\PostRepository $postRepository,
        private readonly App\Model\Mapper\Mapper        $mapper,
    ) {
        parent::__construct($postRepository, $mapper);
    }

}