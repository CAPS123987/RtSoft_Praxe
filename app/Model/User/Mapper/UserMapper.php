<?php
namespace App\Model\User\Mapper;

use App\Model\Role\Facades\RoleFacade;
use App\Model\User\DTO\UserDTO;
use App\Model\Generics\Mapper\Mapper;
use App\Model\User\Facades\UserRoleFacade;
use App\Model\User\Repo\UserRepository;
use Nette;

/**
 * @extends Mapper<UserDTO>
 */
final class UserMapper extends Mapper
{
    public function __construct(
        private readonly RoleFacade $roleFacade,
    )
    {
    }
    public function map(Nette\Database\Table\ActiveRow $row) : UserDTO
    {
        return UserDTO::create(
            id: $row->{UserRepository::ID_COL},
            name: $row->{UserRepository::NAME_COL},
            role: $row->{UserRepository::ROLE_COL},
            password: $row->{UserRepository::PASSWORD_COL},
            resolvedRole: $this->roleFacade->getDTOById(id: $row->{UserRepository::ROLE_COL}),
        );
    }

    /**
     * @param Nette\Database\Table\Selection $selection
     * @return array<UserDTO>
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
     * @return UserDTO
     */
    public function mapArrayToDTO(array $data): UserDTO
    {
        return UserDTO::createFromArray($data);
    }
}
