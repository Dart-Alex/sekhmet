@extends('layouts.app')
@section('content')

<div class="card">
	<header class="card-header">
		<p class="card-header-title">Connexion</p>
	</header>

	<div class="card-content">
		<form class="login-form" method="POST" action="{{ route('login') }}">
			{{ csrf_field() }}

			<div class="field is-horizontal">
				<div class="field-label">
					<label class="label">Adresse Email</label>
				</div>

				<div class="field-body">
					<div class="field">
						<p class="control has-icons-left">
							<input class="input" id="email" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus/>
							<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
						</p>

						@if ($errors->has('email'))
						<p class="help is-danger">
							{{ $errors->first('email') }}
						</p>
						@endif
					</div>
				</div>
			</div>

			<div class="field is-horizontal">
				<div class="field-label">
					<label class="label">Mot de passe</label>
				</div>

				<div class="field-body">
					<div class="field">
						<p class="control has-icons-left">
							<input class="input" id="password" type="password" name="password" placeholder="Mot de passe" required/>
							<span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
						</p>

						@if ($errors->has('password'))
						<p class="help is-danger">
							{{ $errors->first('password') }}
						</p>
						@endif
					</div>
				</div>
			</div>

			<div class="field is-horizontal">
				<div class="field-label"></div>

				<div class="field-body">
					<div class="field">
						<p class="control">
							<label class="checkbox">
                                            <input type="checkbox"
                                                   name="remember" {{ old('remember') ? 'checked' : '' }}> Se souvenir de moi
                                        </label>
						</p>
					</div>
				</div>
			</div>

			<div class="field is-horizontal">
				<div class="field-label"></div>

				<div class="field-body">
					<div class="field is-grouped">
						<div class="control">
							<button type="submit" class="button is-primary">Connexion</button>
						</div>

						<div class="control">
							<a class="button is-info is-outlined" href="{{ route('password.request') }}">
                                            Mot de passe oublié ?
                                        </a>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection
