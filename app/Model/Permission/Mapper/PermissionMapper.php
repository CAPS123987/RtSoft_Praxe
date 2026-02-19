<?php
namespace App\Model\Permission\Mapper;

use App\Model\Generics\Mapper\Mapper;
use App\Model\Permission\DTO\PermissionDTO;
use App\Model\Permission\Repo\PermissionRepository;
use Nette;

/**
 * @extends Mapper<PermissionDTO>
 */
final class PermissionMapper extends Mapper
{
    public function map(Nette\Database\Table\ActiveRow $row) : PermissionDTO
    {
        return PermissionDTO::create(
            id: $row->{PermissionRepository::ID_COL},
            name: $row->{PermissionRepository::NAME_COL},
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<PermissionDTO>
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
     * @return PermissionDTO
     */
    public function mapArrayToDTO(array $data): PermissionDTO
    {
        return PermissionDTO::createFromArray($data);
    }
}

