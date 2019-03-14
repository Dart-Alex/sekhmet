<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- User for javascript -->
	@if(Auth::check())
	<meta name="user" content="{{ json_encode(auth()->user()) }}">
	@endif

	<title>{{ config('app.name', 'Laravel') }}</title>

	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

	<!-- Styles -->
	<link href="{{ mix('css/bulma.css') }}" rel="stylesheet">
	<link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>

<body class="has-navbar-fixed-top">
	<div id="app">
	@include('layouts.navbar')
	{{Breadcrumbs::render()}}
		<main class="container">
			@yield('content')
		</main>
		<!-- Scripts -->
		@if(session('message'))
		<message type="{{ session('message')["type"] }}">{{ session('message')["content"] }}</message>
		@endif
		<logged-in></logged-in>
	</div>
	<script src="{{ mix('js/manifest.js') }}"></script>
	<script src="{{ mix('js/vendor.js') }}"></script>
	<script src="{{ mix('js/app.js') }}"></script>
</body>

</html>
