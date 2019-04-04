@extends('layouts.app')
@section('content')
<div class="content">
	<div class="panel">
		<div class="panel-heading">
			<span class="is-size-3">Canaux</span>
		</div>
		@foreach($chans as $chan)
		@can('view', $chan)
		<a href="{{ route('chans.show', ['chan' => $chan->name]) }}" class="panel-block">
			<span class="is-size-4 has-text-link">{{ $chan->displayName() }}</span>
		</a>
		@endcan
		@endforeach
		@can('create', App\Chan::class)
		<div class="panel-block">
			<a href="{{route('chans.create')}}" class="button is-primary is-outlined is-fullwidth">Créer un nouveau chan</a>
		</div>
		@endcan
	</div>

</div>
@endsection
