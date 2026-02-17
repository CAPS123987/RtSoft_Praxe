<?php

namespace App\Model\DTOs;

interface DTO
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array;
}