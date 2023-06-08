<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class SearchNoteDto
 */
class SortNoteDto
{
    const SORT_ASC = 'asc';
    const SORT_DEST= 'desc';

    #[Assert\Choice([self::SORT_ASC, self::SORT_DEST])]
    public string $createdAt = self::SORT_ASC;
}