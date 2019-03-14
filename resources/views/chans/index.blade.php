@extends('layouts.app')
@section('breadcrumbs', Breadcrumbs::render('home'))
@section('content')
<div class="content">

	<ul>
		@foreach($chans as $chan)
		<li>
		<h2><a href="{{ route('chans.show', ['chan' => $chan->name]) }}">#{{ ucfirst($chan->name) }}</a></h2>
			<p>{{ $chan->description }}</p>
		</li>
		@endforeach
	</ul>
	<a href="{{route('chans.create')}}" class="button is-primary">Créer un nouveau chan</a>
</div>
@endsection
