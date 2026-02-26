<?php

namespace App\Model\Post;
use Nette;

class PostPaginator extends Nette\Utils\Paginator
{
    public function __construct()
    {
        $this->setItemsPerPage(6);
    }

    public function addPaginationToQuery(Nette\Database\Table\Selection $row, int $page) : Nette\Database\Table\Selection
    {
        $this->setPage($page);
        return $row->limit($this->getLength(), $this->getOffset());
    }
}