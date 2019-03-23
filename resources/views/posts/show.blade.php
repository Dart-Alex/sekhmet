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
	<h2>Inscrits</h2>
	<ul>
		@foreach($post->postSubscribers as $subscriber)
		<li>
			{{$subscriber->name}}
			@can('delete', $subscriber)
			<a title="Supprimer" class="fas fa-times has-text-danger" href="{{route('postSubscribers.destroy', ['chan' => $chan->name, 'post' => $post->id, 'postSubscriber' => $subscriber->id])}}" onclick="event.preventDefault();document.getElementById('delete-form-subscriber-{{$subscriber->id}}').submit();">
				</a>
				<form id="delete-form-subscriber-{{$subscriber->id}}" action="{{route('postSubscribers.destroy', ['chan' => $chan->name, 'post' => $post->id, 'postSubscriber' => $subscriber->id])}}" method="POST">
					@csrf
					@method('DELETE')
				</form>
			@endcan
		</li>
		@endforeach
		@can('create', ['App\PostSubscriber', $post])
		<li>
			<form action="{{route('postSubscribers.store', ['chan' => $chan->name, 'post' => $post->id])}}" method="post">
				@csrf
		@auth
			<input type="hidden" name='name' value='{{auth()->user()->name}}'/>
			<div class="field">
				<div class="control">
					<input type="submit" class='button is-primary' value="S'inscrire"/>
				</div>
			</div>
		@endauth
		@guest
			<div class="field is-grouped">
				<div class='control'>
					<input class='input' type='text' name='name' id='name' placeholder='Votre pseudo' value='{{old('name')}}' required/>
				</div>
				<div class="control">
					<input type="submit" class='button is-primary' value="S'inscrire"/>
				</div>
				@if ($errors->has('name'))
				<p class='help is-danger'>
					{{ $errors->first('name') }}
				</p>
				@endif
			</div>
		@endguest
			</form>
		</li>
		@endcan
	</ul>
	@if($post->comments_allowed)
	<comments id="{{$post->id}}"></comments>
	@endif
</div>
@endsection
