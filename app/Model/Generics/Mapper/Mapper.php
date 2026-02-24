<?php

namespace App\Model\Generics\Mapper;

use App\Model\Generics\DTO\DTO;
use Nette;

/**
 * @template T of DTO
 */
abstract class Mapper
{
    /**
     * @param Nette\Database\Table\ActiveRow $row
     * @return T
     */
    abstract public function map(Nette\Database\Table\ActiveRow $row);

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<T>
     */
    abstract public function mapAll(Nette\Database\Table\Selection $selection): array;

    /**
     * @param array<string,Mixed> $data
     * @return T
     */
    abstract public function mapArrayToDTO(array $data);
}