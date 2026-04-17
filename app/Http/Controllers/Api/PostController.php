<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Post\ModeratePostRequest;
use App\Http\Requests\Post\StoreCommentRequest;
use App\Http\Requests\Post\StorePostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(): JsonResponse
    {
        $posts = Post::with(['author', 'tag', 'status', 'media'])->latest()->paginate(15);

        return response()->json(['message' => 'Liste des publications.', 'data' => $posts]);
    }

    public function store(StorePostRequest $request, PostService $postService): JsonResponse
    {
        $post = $postService->createPost($request->validated());

        return response()->json([
            'message' => 'Publication créée avec succès.',
            'data'    => $post->load(['author', 'tag', 'status']),
        ], 201);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['author', 'tag', 'status', 'media', 'comments.author', 'likedByUsers', 'savedByUsers']);

        return response()->json(['message' => 'Détail de la publication.', 'data' => $post]);
    }

    public function moderate(ModeratePostRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $data = $request->validated();

        $result = $data['action'] === 'approve'
            ? $postService->approvePost($post, $data['validator_id'])
            : $postService->rejectPost($post, $data['validator_id'], $data['reason'] ?? null);

        return response()->json(['message' => 'Action effectuée avec succès.', 'data' => $result]);
    }

    public function toggleLike(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        $result = $postService->toggleLike($post, $request->integer('user_id'));

        return response()->json(['message' => 'Action sur le like effectuée.', 'data' => $result]);
    }

    public function toggleSave(Request $request, Post $post, PostService $postService): JsonResponse
    {
        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);

        $result = $postService->toggleSave($post, $request->integer('user_id'));

        return response()->json(['message' => 'Action sur la sauvegarde effectuée.', 'data' => $result]);
    }

    public function addComment(StoreCommentRequest $request, Post $post, PostService $postService): JsonResponse
    {
        $comment = $postService->addComment($post, $request->integer('user_id'), $request->string('text')->toString());

        return response()->json([
            'message' => 'Commentaire ajouté avec succès.',
            'data'    => $comment->load('author'),
        ], 201);
    }
}
