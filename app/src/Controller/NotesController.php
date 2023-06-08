<?php
namespace App\Controller;

use App\Dto\CreateOrUpdateNoteDto;
use App\Dto\SearchNoteDto;
use App\Dto\SortNoteDto;
use App\Http\AbstractBaseController;
use App\Http\Resolver\BodyValue;
use App\Http\Resolver\QueryParameter;
use App\Service\NoteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Ulid;

/**
 * Class NotesController
 */
#[Route('/api/notes', name: 'api_notes_')]
class NotesController extends AbstractBaseController
{
    public function __construct(private readonly NoteService $noteService)
    {}

    /**
     * @param SearchNoteDto $search
     * @param SortNoteDto   $sort
     *
     * @return JsonResponse
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function indexAction(
        #[QueryParameter] SearchNoteDto $search,
        #[QueryParameter] SortNoteDto $sort
    ): JsonResponse
    {
        $notes = $this->noteService->search($search, $sort);

        return $this->json($notes);
    }

    /**
     * @param Ulid $id
     *
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'view', methods: ['GET'])]
    public function viewAction(Ulid $id): JsonResponse
    {
        $note = $this->noteService->find($id);

        return $this->json($note);
    }

    /**
     * @param CreateOrUpdateNoteDto $noteDto
     *
     * @return JsonResponse
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function createAction(#[BodyValue] CreateOrUpdateNoteDto $noteDto): JsonResponse
    {
        $note = $this->noteService->create($noteDto);

        return $this->json($note);
    }

    /**
     * @param Ulid                  $id
     * @param CreateOrUpdateNoteDto $noteDto
     *
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'update', methods: ['POST'])]
    public function updateAction(Ulid $id, #[BodyValue] CreateOrUpdateNoteDto $noteDto): JsonResponse
    {
        $note = $this->noteService->update($id, $noteDto);

        return $this->json($note);
    }

    /**
     * @param Ulid $id
     *
     * @return JsonResponse
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function deleteAction(Ulid $id): JsonResponse
    {
        $note = $this->noteService->delete($id);

        return $this->json($note);
    }
}