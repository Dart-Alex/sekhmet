<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- User for javascript -->
	<?php
	$user = auth()->user();
	$userMeta = (($user)?[
		'id' => $user->id,
		'name' => $user->name,
		'admin' => $user->admin,
		'chan_admin' => ((isset($chan))?$chan->isAdmin($user):false),
	]:null);
	?>
	<meta name="user" content="{{ json_encode($userMeta) }}">

	<title>@yield('title', config('app.name', 'Laravel'))</title>

	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

	<!-- Styles -->
	<link href="{{ mix('css/vendor.css') }}" rel="stylesheet">
	<link href="{{ mix('css/app.css') }}" rel="stylesheet">

	<!-- Favicon -->
	<link rel="Shortcut Icon" href="{{ asset('favicon.ico')}}" type="image/x-icon"/>
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
	<script>
	window.addEventListener("load", function(){
	window.cookieconsent.initialise({
	"palette": {
		"popup": {
		"background": "#edeff5",
		"text": "#838391"
		},
		"button": {
		"background": "#4b81e8"
		}
	},
	"position": "top",
	"static": true,
	"content": {
		"message": "Ce site utilise des cookies pour vous offrir une expérience utilisateur de qualité. En poursuivant votre navigation sur ce site, vous acceptez l’utilisation de cookies.",
		"dismiss": "Ok !",
		"link": ""
	}
	})});
	</script>
</head>

<body class="has-navbar-fixed-top">
	<div id="app">
		@if(session('message'))
		<message type="{{ session('message')["type"] }}" content="{{ session('message')["content"] }}"></message>
		@endif
		@include('layouts.navbar')
		<div class="container" style="padding-top:20px;">
			{{Breadcrumbs::render()}}
		</div>
		<main class="container">
			@yield('content')
		</main>

	</div>
	<footer class="footer">
		<div class="content has-text-centered">
			<a href="{{route('polconf')}}">Politique de confidentialité.</a><br/>

			Site développé par <a class="no-modify" href="https://github.com/Dart-Alex/" target="_blank">Dart-Alex</a>.<br/>
			<a class="no-modify" href="https://bulma.io/made-with-bulma/" target="_blank">
				<img src="https://bulma.io/images/made-with-bulma.png" alt="Made with Bulma" width="128" height="24">
			</a>
		</div>
	</footer>
	<!-- Scripts -->
	<script src="{{ mix('js/manifest.js') }}"></script>
	<script src="{{ mix('js/vendor.js') }}"></script>
	<script src="{{ mix('js/app.js') }}"></script>
	@yield('scripts', '')
</body>

</html>
