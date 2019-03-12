@extends('layouts.app')
@section('content')
<div class="content">
	<div class="title m-b-md">
		Laravel
		<p class="versioninfo">Version {{ app()->version() }}</p>
	</div>

	<div class="links">
		<a href="https://laravel.com/docs">Documentation</a>
		<a href="https://laracasts.com">Laracasts</a>
		<a href="https://laravel-news.com">News</a>
		<a href="https://forge.laravel.com">Forge</a>
		<a href="https://github.com/laravel/laravel">GitHub</a>
	</div>

	<div class="foundation_button_test">
		<p class="framwork_title">Bulma v0.7.4</p>
		<p class="framwork_title">Bulma Extension v4.0.2</p>

		<div class="block">
			<a class="button is-primary">Primary</a>
			<a class="button is-info">Info</a>
			<a class="button is-success">Success</a>
			<a class="button is-warning">Warning</a>
			<a class="button is-danger">Danger</a>
		</div>
	</div>
</div>
@endsection
