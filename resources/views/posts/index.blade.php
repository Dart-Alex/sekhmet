@extends('layouts.app')
@section('content')
<div class="content">
	@can('create', ['App\Post', $chan])
	<a href='{{route('posts.create', ['chan' => $chan->name])}}' class='button is-primary'>Créer un event</a>
	@endcan
	@if($posts->count() > 0)
	<h1>A venir</h1>
	@foreach ($posts as $post)
		@include('posts.post-index', ['post' => $post, 'chan' => $chan])
	@endforeach
	@endif

	@if ($oldPosts->count() > 0)
	<h1>Anciens</h1>
	@foreach ($oldPosts as $post)
		@include('posts.post-index', ['post' => $post, 'chan' => $chan])
	@endforeach
	@endif
</div>
@endsection
