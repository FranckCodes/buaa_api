<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\ModeratePostRequest;
use App\Http\Requests\Post\StoreCommentRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        return $this->paginatedResponse(
            Post::with(['author', 'tag', 'status', 'media'])
                ->withCount(['likedByUsers', 'savedByUsers'])
                ->latest()
                ->paginate(15),
            'Liste des publications récupérée avec succès.',
            fn ($post) => new PostResource($post)
        );
    }

    public function store(StorePostRequest $request, PostService $postService): JsonResponse
    {
        $this->authorize('create', Post::class);

        $post = $postService->createPost($request->validated());

        return $this->successResponse(
            new PostResource($post->load(['author', 'tag', 'status'])),
            'Publication créée avec succès.',
            201
        );
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['author', 'tag', 'status', 'validatedBy', 'media', 'comments.author'])
            ->loadCount(['likedByUsers', 'savedByUsers']);

        return $this->successResponse(
            new PostResource($post),
            'Détail de la publication récupéré avec succès.'
        );
    }

    public function moderate(ModeratePostRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('moderate', $post);

        $data   = $request->validated();
        $result = $data['action'] === 'approve'
            ? $postService->approvePost($post, $data['validator_id'])
            : $postService->rejectPost($post, $data['validator_id'], $data['reason'] ?? null);

        return $this->successResponse(new PostResource($result), 'Action sur la publication effectuée avec succès.');
    }

    public function toggleLike(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('interact', Post::class);
        $request->validate(['user_id' => ['required', 'string', 'exists:users,id']]);

        return $this->successResponse(
            $postService->toggleLike($post, $request->string('user_id')->toString()),
            'Action sur le like effectuée avec succès.'
        );
    }

    public function toggleSave(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('interact', Post::class);
        $request->validate(['user_id' => ['required', 'string', 'exists:users,id']]);

        return $this->successResponse(
            $postService->toggleSave($post, $request->string('user_id')->toString()),
            'Action sur la sauvegarde effectuée avec succès.'
        );
    }

    public function addComment(StoreCommentRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('interact', Post::class);

        $comment = $postService->addComment(
            $post,
            $request->string('user_id')->toString(),
            $request->string('text')->toString()
        );

        return $this->successResponse(new CommentResource($comment), 'Commentaire ajouté avec succès.', 201);
    }
}
