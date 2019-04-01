@extends('layouts.app')
@section('content')
<div class="content">
	@can('create', App\Chan::class)
	<a href="{{route('chans.create')}}" class="button is-primary">Créer un nouveau chan</a>
	@endcan
	<ul class="no-bullet">
		@foreach($chans as $chan)
		@can('view', $chan)
		<li>
		<h2><a href="{{ route('chans.show', ['chan' => $chan->name]) }}">{{ $chan->displayName() }}</a></h2>
			<p>{{ $chan->description }}</p>
		</li>
		@endcan
		@endforeach
	</ul>

</div>
@endsection
