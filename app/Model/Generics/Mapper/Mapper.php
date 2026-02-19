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
     * @return DTO
     */
    abstract public function map(Nette\Database\Table\ActiveRow $row) : DTO;

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<DTO>
     */
    abstract public function mapAll(Nette\Database\Table\Selection $selection): array;

    /**
     * @param array<string,Mixed> $data
     * @return DTO
     */
    abstract public function mapArrayToDTO(array $data): DTO;
}