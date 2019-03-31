<nav class="navbar is-fixed-top" id="main-navbar">
		<div class="container">
			<div class="navbar-brand">
				<a class="navbar-item" href="{{ url('/') }}">
					<span class="icon"><i class="fas fa-home"></i></span>
					<span>{{ config('app.name', 'Laravel') }}</span>
				</a>
				<a role="button" aria-label="menu" aria-expanded="false" data-target="navbarMenu" class="navbar-burger burger">
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
				</a>
			</div>
			<div class="navbar-menu" id="navbarMenu">
				<div class="navbar-start">
					<div class="navbar-item has-dropdown" data-target="chanMenu">
						<a href="#" class="navbar-link" id="chanDropdown">
							<span class="icon"><i class="fas fa-hashtag"></i></span>
							<span>Chans</span>
						</a>
						<div class="navbar-dropdown is-boxed" id="chanMenu">
							<?php
								if(!auth()->guest() && auth()->user()->isAdmin()) $chansMenu = \App\Chan::orderBy('name', 'ASC')->get();
								else $chansMenu = \App\Chan::orderBy('name', 'ASC')->where('hidden', false)->get();
							?>
							@foreach ($chansMenu as $chan)
							<a href="{{ route('chans.show', ['chan' => $chan->name]) }}" class="navbar-item">{{$chan->displayName()}}</a>
							@endforeach
						</div>
					</div>
					<a href="{{ route('contact') }}" class="navbar-item">
						<span class="icon"><i class="fas fa-envelope"></i></span>
						<span>Contact</span>
					</a>
					@can('index', 'App\User')
					<a href="{{ route('users.index') }}" class="navbar-item">
						<span class="icon"><i class="fas fa-users"></i></span>
						<span>Utilisateurs</span>
					</a>
					@endcan
				</div>
				<div class="navbar-end">

					<!-- Authentication Links -->
					@guest
					<a class="navbar-item" href="{{ route('login') }}">
						<span class="icon"><i class="fas fa-sign-in-alt"></i></span>
						<span>{{ __('Login') }}</span>
					</a>

					@if (Route::has('register'))
					<a class="navbar-item" href="{{ route('register') }}">
						<span class="icon"><i class="fas fa-plus"></i></span>
						<span>{{ __('Register') }}</span>
					</a>
					@endif @else
					<div class="navbar-item has-dropdown" data-target="userMenu">
						<a id="userDropdown" href="#" class="navbar-link">
							<span class="icon"><i class="fas fa-user"></i></span>
							<span>{{ Auth::user()->name }}</span>
						</a>
						<div class="navbar-dropdown is-boxed is-right" id="userMenu">
							<a href="{{ route('logout') }}" class="navbar-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
								{{ __('Logout') }}
							</a>
							<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									@csrf
							</form>
							<a href='{{ route('profile', ['user' => auth()->user()->id]) }}' class='navbar-item'>Profil</a>
						</div>
					</div>
					@endguest
				</div>
			</div>
		</div>
	</nav>
