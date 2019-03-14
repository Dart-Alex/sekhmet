@extends('layouts.app')
@section('breadcrumbs', Breadcrumbs::render('home'))
@section('content')
<div class="content">

	<ul>
		@foreach($chans as $chan)
		<li>
		<h2><a href="{{ route('chans.show', ['chan' => $chan->name]) }}">{{ $chan->displayName() }}</a></h2>
			<p>{{ $chan->description }}</p>
		</li>
		@can('delete', $chan, App\Chan::class)
		<form action="{{ route('chans.destroy', ['chan' => $chan->name]) }}" method="POST">
			@csrf
			@method('DELETE')
			<input type="submit" class="button is-danger" value="Supprimer le chan"/>
		</form>
		@endcan
		@can('update', $chan, App\Chan::class)
		<form action="{{ route('chans.update', ['chan' => $chan->name]) }}" method="POST">
			@csrf
			@method('PATCH')
			@if($chan->hidden)
			<input type="submit" class="button is-primary" value="Démasquer le chan"/>
			@else
			<input type="hidden" name="hidden" value="on"/>
			<input type="submit" value="Masquer le chan" class="button is-warning"/>
			@endif
		</form>
		@endcan
		@endforeach
	</ul>
	@can('create', App\Chan::class)
	<a href="{{route('chans.create')}}" class="button is-primary">Créer un nouveau chan</a>
	@endcan
</div>
@endsection
