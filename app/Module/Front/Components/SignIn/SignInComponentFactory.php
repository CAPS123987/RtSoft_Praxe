<?php

namespace App\Module\Front\Components\SignIn;

class SignInComponentFactory
{
    public function create(): SignInComponent
    {
        return new SignInComponent();
    }
}

