<?php

namespace App\Model\User\Auth;

use Nette;
use Nette\Security\SimpleIdentity;

class DbAuthenticator implements Nette\Security\Authenticator
{
    public function __construct(
        private Nette\Database\Explorer $database,
        private Nette\Security\Passwords $passwords,
    ) {
    }

    public function authenticate(string $username, string $password): SimpleIdentity
    {
        $row = $this->database->table('users')
            ->where('username', $username)
            ->fetch();

        if (!$row) {
            throw new Nette\Security\AuthenticationException('User not found.');
        }

        if (!$this->passwords->verify($password, $row->password)) {
            throw new Nette\Security\AuthenticationException('Invalid password.');
        }

        return new SimpleIdentity(
            $row->id,
            $row->role, // nebo pole více rolí
            ['name' => $row->username],
        );
    }

    public function isAllowed($role, $resource, $operation): bool
    {
        if ($role === 'admin') {
            return true;
        }
        if ($role === 'user' && $resource === 'article') {
            return true;
        }

        // ...

        return false;
    }
}