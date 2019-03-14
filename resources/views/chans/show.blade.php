@extends('layouts.app')
@section('breadcrumbs', Breadcrumbs::render('chans.show', $chan))
@section('content')
<div class="content">

	<h1>#{{ ucfirst($chan->name) }}</h1>
	<p>{{ $chan->description }}</p>
	<ul>
		<li><a href="">Evenements</a></li>
		<li><a href="">Youtube</a></li>
		<li><a href="">Contacter les administrateurs</a></li>
	</ul>
</div>
@endsection
