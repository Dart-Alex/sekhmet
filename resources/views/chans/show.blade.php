@extends('layouts.app')
@section('content')
<div class="content">

	<h1>{{ $chan->displayName() }}</h1>
	<p>{{ $chan->description }}</p>
	<ul>
		<li><a href="{{route('posts.index', ['chan' => $chan->name])}}">Evenements</a></li>
		<li><a href="">Youtube</a></li>
		<li><a href="">Contacter les administrateurs</a></li>
	</ul>
	<div class='field is-grouped'>
		@can('join', $chan)
		<div class="control">
			<a href="#" class="button is-primary">Rejoindre le chan</a>
		</div>
		@endcan
		@can('update', $chan)
		<div class="control">
			<a href="{{route('chans.edit', ['chan' => $chan->name])}}" class="button is-warning">Modifier le chan</a>
		</div>
		@endcan
		@can('delete', $chan)
		<form action="{{route('chans.destroy', ['chan' => $chan->name])}}" method="POST">
			@csrf
			@method('DELETE')
			<div class="control">
				<input type="submit" class="button is-danger" value="Supprimer le chan"/>
			</div>
		</form>
		@endcan
	</div>
</div>
@endsection
