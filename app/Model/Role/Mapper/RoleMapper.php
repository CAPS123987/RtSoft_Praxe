<?php
namespace App\Model\Role\Mapper;

use App\Model\Generics\Mapper\Mapper;
use App\Model\Role\DTO\RoleDTO;
use App\Model\Role\Repo\RoleRepository;
use Nette;

/**
 * @extends Mapper<RoleDTO>
 */
final class RoleMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : RoleDTO
    {
        return RoleDTO::create(
            id: $row->{RoleRepository::ID_COL},
            name: $row->{RoleRepository::NAME_COL},
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<RoleDTO>
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
     * @return RoleDTO
     */
    public function mapArrayToDTO(array $data): RoleDTO
    {
        return RoleDTO::createFromArray($data);
    }
}

