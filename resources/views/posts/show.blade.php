@extends('layouts.app')
@section('content')
<div class="content">
	<h1>{{$post->name}}</h1>
	<p>Aura lieu {{ $post->date->diffForHumans() }} ({{$post->date->isoFormat('LLLL')}})</p>
	<p>{!! $post->content !!}</p>
	@can('update', $post)
	<div class="field is-grouped is-fullwidth">
		<div class="control is-expanded">
			<a href="{{route('posts.edit', ['chan' => $chan->name, 'post' => $post->id])}}" class='button is-warning is-outlined is-fullwidth'>Modifier l'event</a>
		</div>
		<div class="control is-expanded">
			<a href="{{route('posts.destroy', ['chan' => $chan->name, 'post' => $post->id])}}" class="button is-danger is-outlined is-fullwidth" onclick="event.preventDefault();document.getElementById('delete-form-{{$post->id}}').submit();">Supprimer l'event</a>
			<form id="delete-form-{{$post->id}}" action="{{route('posts.destroy', ['chan' => $chan->name, 'post' => $post->id])}}" method="post">
				@csrf
				@method('DELETE')
			</form>
		</div>
	</div>
	@endcan
	<div class="panel">
		<div class="panel-heading">
			Inscrits
		</div>

		@foreach($post->postSubscribers as $subscriber)
		<div class="panel-block">
			{{$subscriber->name}}
			@can('delete', $subscriber)
			<a style="flex-grow:1;justify-content:flex-end;display:inline-flex;" title="Supprimer" class="has-text-danger" href="{{route('postSubscribers.destroy', ['chan' => $chan->name, 'post' => $post->id, 'postSubscriber' => $subscriber->id])}}" onclick="event.preventDefault();document.getElementById('delete-form-subscriber-{{$subscriber->id}}').submit();">
				<span class="icon"><i class="fas fa-times"></i></span>
			</a>
				<form id="delete-form-subscriber-{{$subscriber->id}}" action="{{route('postSubscribers.destroy', ['chan' => $chan->name, 'post' => $post->id, 'postSubscriber' => $subscriber->id])}}" method="POST">
					@csrf
					@method('DELETE')
				</form>
			@endcan
			</div>
		@endforeach
		@can('create', ['App\PostSubscriber', $post])
		<div class="panel-block">
			<form action="{{route('postSubscribers.store', ['chan' => $chan->name, 'post' => $post->id])}}" method="post">
				@csrf
		@auth
			<input type="hidden" name='name' value='{{auth()->user()->name}}'/>
				<div class="control">
					<input type="submit" class='button is-primary' value="S'inscrire"/>
				</div>
		@endauth
		{{-- @guest
			<div class="field is-grouped">
				<div class='control has-icons-left'>
					<input class='input' type='text' name='name' id='name' placeholder='Votre pseudo' value='{{old('name')}}' required/>
					<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
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
		@endguest --}}
		@guest
			<div>
				Pour rejoindre l'event sans vous inscrire, !event join sur irc.
			</div>
		@endguest
			</form>
		</div>
		@endcan
	</div>

	@if($post->comments_allowed)
	<comments id="{{$post->id}}"></comments>
	@endif
</div>
@endsection
