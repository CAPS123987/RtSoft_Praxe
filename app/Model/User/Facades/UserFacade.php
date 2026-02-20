<?php

namespace App\Model\User\Facades;

use App;
use App\Model\User\DTO\UserDTO;
use App\Model\Generics\Facades\DTOFacade;

/**
 * @extends DTOFacade<UserDTO>
 */
final class UserFacade extends DTOFacade
{
    public function __construct(
        private readonly App\Model\User\Repo\UserRepository $userRepository,
        private readonly App\Model\User\Mapper\UserMapper   $userMapper,
    ) {
        parent::__construct($userRepository, $userMapper);
    }
}