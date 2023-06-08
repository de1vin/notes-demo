<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CreateOrUpdateNoteDto
 */
class CreateOrUpdateNoteDto
{
    #[Assert\NotBlank(normalizer: 'trim')]
    #[Assert\Length(min: 3, max: 250)]
    public string $title;

    #[Assert\NotBlank(normalizer: 'trim')]
    public string $content;
}