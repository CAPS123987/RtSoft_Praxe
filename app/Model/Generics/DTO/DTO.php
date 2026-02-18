<?php

namespace App\Model\Generics\DTO;

interface DTO
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}