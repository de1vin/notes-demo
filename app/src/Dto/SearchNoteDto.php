<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SearchNoteDto
 */
class SearchNoteDto
{
    #[Assert\Length(min: 3, max: 250)]
    public string|null $title;
}