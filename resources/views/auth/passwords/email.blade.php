@extends('layouts.app')

@section('content')


<div class="card">
	<header class="card-header">
		<p class="card-header-title">Réinitialisation du mot de passe</p>
	</header>

	<div class="card-content">
		@if (session('status'))
				{{ success(session('status')) }}
		@endif

		<form class="forgot-password-form" method="POST" action="{{ route('password.email') }}">

			{{ csrf_field() }}

			<div class="field is-horizontal">
				<div class="field-label">
					<label class="label">Adresse Email</label>
				</div>

				<div class="field-body">
					<div class="field">
						<p class="control has-icons-left">
							<input class="input" id="email" type="email" name="email" placeholder='Email'
									value="{{ old('email') }}" required autofocus/>
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
				<div class="field-label"></div>

				<div class="field-body">
					<div class="field is-grouped">
						<div class="control">
							<button type="submit" class="button is-primary">Envoyer le lien de réinitialisation
							</button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

@endsection
