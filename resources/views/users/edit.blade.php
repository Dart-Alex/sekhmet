@extends('layouts.app')
@section('content')
<div class="box content">
	<h3>Profil</h3>
	<form action="{{route('users.update',['user' => $user->id])}}" method="POST">
		@csrf
		@method('PATCH')
		<div class='field is-horizontal'>
			<div class="field-label is-normal">
				<label for='name' class='label'>Nom</label>
			</div>
			<div class="field-body">
				<div class="field">
					<div class='control'>
						<input class='input' type='text' name='name' id='name' value='{{old('name', $user->name)}}' required/>
					</div>
				</div>
			</div>
			@if ($errors->has('name'))
			<p class='help is-danger'>
				{{ $errors->first('name') }}
			</p>
			@endif
		</div>
		<div class='field is-horizontal'>
			<div class='field-label is-normal'>
				<label for='email' class='label'>Email</label>
			</div>
			<div class='field-body'>
				<div class='field'>
					<div class='control is-expanded'>
						<input class='input' type='email' name='email' id='email' placeholder='Email' value='{{old('email', $user->email)}}' required/>
					</div>
					@if($errors->has('email'))
					<p class='help is-danger'>
						{{ $errors->first('email') }}
					</p>
					@endif
				</div>
				<div class='field'>
					<div class='control is-expanded'>
						<input class='input' type='email' name='email_confirmation' id='email_confirmation' placeholder="Confirmation" value='{{old('email_confirmation', $user->email)}}' required/>
					</div>
					@if($errors->has('email_confirmation'))
					<p class='help is-danger'>
						{{ $errors->first('email_confirmation') }}
					</p>
					@endif
				</div>
			</div>
		</div>
		@if(false)
		<div class='field is-horizontal'>
			<div class='field-label is-normal'>
				<label for='password' class='label'>Mot de passe</label>
			</div>
			<div class='field-body'>
				<div class='field'>
					<div class='control is-expanded'>
						<input class='input' type='password' name='password' id='password' placeholder='Nouveau mot de passe' value='{{old('password')}}'/>
					</div>
					@if($errors->has('password'))
					<p class='help is-danger'>
						{{ $errors->first('password') }}
					</p>
					@endif
				</div>
				<div class='field'>
					<div class='control is-expanded'>
						<input class='input' type='password' name='password_confirmation' id='password_confirmation' placeholder='Confirmation' value='{{old('password_confirmation')}}'/>
					</div>
					@if($errors->has('password_confirmation'))
					<p class='help is-danger'>
						{{ $errors->first('password_confirmation') }}
					</p>
					@endif
				</div>
			</div>
		</div>
		@endif
		@if(auth()->user()->isAdmin())
		<div class='field is-horizontal'>
			<div class='field-label is-normal'><label class='label'></label></div>
			<div class='field-body'>
				<div class='field'>
					<div class='control'>
						<label class='checkbox'>
							<input type='checkbox' name='admin' {{(old('admin', $user->admin)?'checked':'')}}/>
							Admin
						</label>
					</div>
				</div>
			</div>
		</div>
		@endif
		<div class="field is-horizontal">
				<div class="field-label"></div>

				<div class="field-body">
					<div class="field is-grouped">
						<div class="control">
							<button type="submit" class="button is-primary">Modifier</button>
						</div>
					</div>
				</div>
			</div>

	</form>
	<h3>Pseudos irc</h3>
	<ul>
		@foreach($user->ircNames as $ircName)
		<li>{{$ircName->name}}</li>
		@endforeach
	</ul>
</div>
@endsection
