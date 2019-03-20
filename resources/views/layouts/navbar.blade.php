<nav class="navbar is-fixed-top" id="main-navbar">
		<div class="container">
			<div class="navbar-brand">
				<a class="navbar-item" href="{{ url('/') }}">
						{{ config('app.name', 'Laravel') }}
				</a>
				<a role="button" aria-label="menu" aria-expanded="false" data-target="navbarMenu" class="navbar-burger burger">
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
					<span aria-hidden="true"></span>
				</a>
			</div>
			<div class="navbar-menu" id="navbarMenu">
				<div class="navbar-start">

				</div>
				<div class="navbar-end">
					<!-- Authentication Links -->
					@guest
					<a class="navbar-item" href="{{ route('login') }}">{{ __('Login') }}</a>

					@if (Route::has('register'))
					<a class="navbar-item" href="{{ route('register') }}">{{ __('Register') }}</a>
					@endif @else
					<div class="navbar-item has-dropdown" data-target="userMenu">
						<a id="navbarDropdown" href="#" class="navbar-link">{{ Auth::user()->name }} <span class="caret"></span></a>
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
