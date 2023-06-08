<?php

namespace App\Service;

use App\Dto\CreateOrUpdateNoteDto;
use App\Dto\SearchNoteDto;
use App\Dto\SortNoteDto;
use App\Dto\ViewNoteDto;
use App\Entity\Note;
use App\Exception\ValidationException;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class NoteService
 */
readonly class NoteService
{
    const SHORT_CONTENT_LEN = 200;
    /**
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface     $validator
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface     $validator
    ) {}

    /**
     * @param SearchNoteDto $searchDto
     * @param SortNoteDto   $sortDto
     * @param bool          $shortContent
     *
     * @return array<ViewNoteDto>
     */
    public function search(SearchNoteDto $searchDto, SortNoteDto $sortDto, bool $shortContent = true): array
    {
        $qb = $this->entityManager->createQueryBuilder();

        $qb
            ->select('u')
            ->from(Note::class, 'u');

        $this->applySearchFilter($searchDto, $qb);
        $this->applySortOrder($sortDto, $qb);

//        dd($qb->getQuery()->getSQL());
        /** @var Note[] $notes */
        $notes = $qb->getQuery()->execute();
        $result = [];

        foreach ($notes as $note) {
            $result[] = $this->mapOnDto($note, $shortContent);
        }

        return $result;
    }

    /**
     * @param string $id
     *
     * @return ViewNoteDto
     */
    public function find(string $id): ViewNoteDto
    {
        $note = $this->findOr404($id);

        return $this->mapOnDto($note, false);
    }

    /**
     * @param CreateOrUpdateNoteDto $noteDto
     * @param bool                  $flush
     *
     * @return ViewNoteDto
     */
    public function create(CreateOrUpdateNoteDto $noteDto, bool $flush = true): ViewNoteDto
    {
        $errors = $this->validator->validate($noteDto);

        if (count($errors)) {
            throw new ValidationException($errors);
        }

        $note = new Note();
        $date = new DateTimeImmutable();

        $note->setTitle(trim($noteDto->title))
            ->setContent(trim($noteDto->content))
            ->setCreatedAt($date)
            ->setUpdatedAt($date);

        $this->entityManager->persist($note);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $this->mapOnDto($note, false);
    }

    /**
     * @param string                $id
     * @param CreateOrUpdateNoteDto $noteDto
     * @param bool                  $flush
     *
     * @return ViewNoteDto
     */
    public function update(string $id, CreateOrUpdateNoteDto $noteDto, bool $flush = true): ViewNoteDto
    {
        $errors = $this->validator->validate($noteDto);

        if (count($errors)) {
            throw new ValidationException($errors);
        }

        $note = $this->findOr404($id);
        $date = new DateTimeImmutable();

        $note->setTitle(trim($noteDto->title))
            ->setContent(trim($noteDto->content))
            ->setUpdatedAt($date);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $this->mapOnDto($note, false);
    }

    /**
     * @param string $id
     * @param bool   $flush
     *
     * @return ViewNoteDto
     */
    public function delete(string $id, bool $flush = true): ViewNoteDto
    {
        $note = $this->findOr404($id);
        $noteDto = $this->mapOnDto($note, false);

        $this->entityManager->remove($note);

        if ($flush) {
            $this->entityManager->flush();
        }

        return $noteDto;
    }

    /**
     * @param string $id
     *
     * @return Note
     */
    private function findOr404(string $id): Note
    {
        $repository = $this->entityManager->getRepository(Note::class);
        $note = $repository->find($id);

        if ($note === null) {
            throw new NotFoundHttpException('Record not found');
        }

        return $note;
    }

    /**
     * @param Note $note
     * @param bool $shortContent
     *
     * @return ViewNoteDto
     */
    private function mapOnDto(Note $note, bool $shortContent): ViewNoteDto
    {
        $dto = new ViewNoteDto();
        $dto->id = $note->getId();
        $dto->title = $note->getTitle();
        $dto->content = $note->getContent();
        $dto->createdAt = $note->getCreatedAt()->format('c');

        if ($shortContent) {
            $dto->content = mb_substr($dto->content, 0, self::SHORT_CONTENT_LEN);
        }

        return $dto;
    }

    /**
     * @param SearchNoteDto $searchDto
     * @param QueryBuilder  $qb
     *
     * @return void
     */
    private function applySearchFilter(SearchNoteDto $searchDto, QueryBuilder $qb): void
    {
        $errors = $this->validator->validate($searchDto);

        if (count($errors)) {
            throw new ValidationException($errors);
        }

        if (!empty($searchDto->title)) {
            $qb->andWhere($qb->expr()->like('u.title', ':title'));
            $qb->setParameter('title', "%$searchDto->title%");
        }
    }

    /**
     * @param SortNoteDto  $sortDto
     * @param QueryBuilder $qb
     *
     * @return void
     */
    private function applySortOrder(SortNoteDto $sortDto, QueryBuilder $qb): void
    {
        $errors = $this->validator->validate($sortDto);

        if (count($errors)) {
            throw new ValidationException($errors);
        }

        $qb->addOrderBy('u.createdAt', $sortDto->createdAt);
    }
}