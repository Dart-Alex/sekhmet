<?php

namespace App\Api\Controllers;

use App\Comment;
use Illuminate\Http\Request;
use App\Post;
use Illuminate\Support\Facades\Cache;

class CommentController extends Controller
{
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	public function index(Post $post)
	{
		if(!$comments = Cache::get('comments-'.$post->id)) {
			$comments = Comment::where('post_id', $post->id)->orderBy('created_at', 'ASC')->get()->toArray();
			Cache::put('comments-'.$post->id, $comments, now()->addDay());
		};

		return response()->json($comments);
	}

	/**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function store(Request $request)
	{
		$this->authorize('create', Comment::class);
		$validated = $this->validate($request, [
			'name' => 'required|string',
			'content' => 'required|string',
			'reply_to' => 'nullable|numeric|exists:comments,id',
			'post_id' => 'required|numeric|exists:posts,id'
		]);
		if(array_key_exists('reply_to', $validated)) {
			$repliedToComment = Comment::findOrFail($validated['reply_to']);
			if ($repliedToComment->post_id != $validated['post_id']) {
				return ["message" => 'Le commentaire auquel vous essayez de répondre n\'appartient pas au même événement.'];
			}
		}
		$comment = Comment::create($validated);
		return [
			'message' => [
				'type' => 'success',
				'content' => 'Commentaire ajouté.'
			],
			'comment' => $comment
		];
	}

	/**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
	public function update(Request $request, Comment $comment)
	{
		$this->authorize('update', $comment);
		$validated = $this->validate($request, [
			'name' => 'required|string',
			'content' => 'required|string'
		]);
		$comment->update($validated);
		return [
			'message' => [
				'type' => 'success',
				'content' => 'Commentaire modifié.'
			],
			'comment' => $comment
		];
	}

	/**
     * Remove the specified resource from storage.
     *
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     */
	public function destroy(Comment $comment)
	{
		$this->authorize('delete', $comment);
		$commentCopy = $comment->toArray();
		$comment->delete();
		return [
			'message' => [
				'type' => 'success',
				'content' => 'Commentaire supprimé.'
			],
			'comment' => $commentCopy
		];
	}
}
