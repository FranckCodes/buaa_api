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
        return PostResource::collection(
            Post::with(['author', 'tag', 'status', 'media'])->latest()->paginate(15)
        )->additional(['message' => 'Liste des publications.'])->response();
    }

    public function store(StorePostRequest $request, PostService $postService): JsonResponse
    {
        $this->authorize('create', Post::class);
        $post = $postService->createPost($request->validated());

        return response()->json([
            'message' => 'Publication créée avec succès.',
            'data'    => new PostResource($post->load(['author', 'tag', 'status'])),
        ], 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json([
            'message' => 'Détail de la publication.',
            'data'    => new PostResource($post->load(['author', 'tag', 'status', 'media', 'comments.author'])),
        ]);
    }

    public function moderate(ModeratePostRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('moderate', $post);
        $data = $request->validated();

        $result = $data['action'] === 'approve'
            ? $postService->approvePost($post, $data['validator_id'])
            : $postService->rejectPost($post, $data['validator_id'], $data['reason'] ?? null);

        return response()->json(['message' => 'Action effectuée avec succès.', 'data' => new PostResource($result)]);
    }

    public function toggleLike(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('interact', $post);
        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        return response()->json([
            'message' => 'Action sur le like effectuée.',
            'data'    => $postService->toggleLike($post, $request->integer('user_id')),
        ]);
    }

    public function toggleSave(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('interact', $post);
        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        return response()->json([
            'message' => 'Action sur la sauvegarde effectuée.',
            'data'    => $postService->toggleSave($post, $request->integer('user_id')),
        ]);
    }

    public function addComment(StoreCommentRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $this->authorize('interact', $post);
        $comment = $postService->addComment($post, $request->integer('user_id'), $request->string('text')->toString());

        return response()->json([
            'message' => 'Commentaire ajouté avec succès.',
            'data'    => new CommentResource($comment->load('author')),
        ], 201);
    }
}
