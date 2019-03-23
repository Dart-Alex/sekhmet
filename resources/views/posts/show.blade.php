@extends('layouts.app')
@section('content')
<div class="content">
	<h1>{{$post->name}}</h1>
	<p>Aura lieu {{ $post->date->diffForHumans() }} ({{$post->date->isoFormat('LLLL')}})</p>
	<p>{!! $post->content !!}</p>
	@can('update', $post)
	<div class="field is-grouped">
		<div class="control">
			<a href="{{route('posts.edit', ['chan' => $chan->name, 'post' => $post->id])}}" class='button is-warning'>Modifier l'event</a>
		</div>
		<div class="control">
			<a href="{{route('posts.destroy', ['chan' => $chan->name, 'post' => $post->id])}}" class="button is-danger" onclick="event.preventDefault();document.getElementById('delete-form-{{$post->id}}').submit();">Supprimer l'event</a>
			<form id="delete-form-{{$post->id}}" action="{{route('posts.destroy', ['chan' => $chan->name, 'post' => $post->id])}}" method="post">
				@csrf
				@method('DELETE')
			</form>
		</div>
	</div>

	@endcan
	@if($post->comments_allowed)
	<comments id="{{$post->id}}"></comments>
	@endif
</div>
@endsection
