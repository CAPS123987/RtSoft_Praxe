<?php

namespace App\Model\User\Auth;

use App\Model\Role\Facades\RolePermissionFacade;
use App\Model\User\Facades\UserFacade;
use App\Model\User\Repo\UserRepository;
use Nette;

class DbAuthenticator implements Nette\Security\Authenticator
{
    public function __construct(
        private Nette\Security\Passwords $passwords,
        private RolePermissionFacade $rolePermissionFacade,
        private UserFacade $userFacade,
        private UserRepository $userRepository,
    ) {
    }

    public function authenticate(string $username, string $password): BlogIdentity
    {
        try {
            $user = $this->userFacade->getDTOByX(UserRepository::NAME_COL, $username);
        } catch (\RuntimeException $e) {
            throw new Nette\Security\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $user->password)) {
            throw new Nette\Security\AuthenticationException('Invalid password.');
        }

        // Aktualizujeme last_login na aktuální čas
        if ($user->id !== null) {
            $this->userRepository->update($user->id, [
                UserRepository::LAST_LOGIN_COL => new \DateTime(),
            ]);
        }

        $permissions = $this->rolePermissionFacade->getRolePermissionsByRoleId($user->role);

        return new BlogIdentity(
            $user->id ?? -1,
            $permissions,
            $user->role,
            ['name' => $user->name],
        );
    }
}