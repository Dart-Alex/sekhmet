@extends('layouts.app')

@section('content')
    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Inscription
                </h1>
            </div>
        </div>
    </section>

    <div>
        <div>
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">Inscription</p>
                </header>

                <div class="card-content">
                    <form class="register-form" method="POST" action="{{ route('register') }}">

                        {{ csrf_field() }}

                        <div class='field is-horizontal'>
							<div class='field-label is-normal'>
								<label for='name' class='label'>Nom</label>
							</div>
							<div class='field-body'>
								<div class='field'>
									<div class='control'>
										<input class='input' type='text' name='name' id='name' placeholder='Nom' value='{{old('name')}}' required/>
									</div>
									@if($errors->has('name'))
									<p class='help is-danger'>
										{{ $errors->first('name') }}
									</p>
									@endif
								</div>
							</div>
						</div>

                        <div class='field is-horizontal'>
							<div class='field-label is-normal'>
								<label for='email' class='label'>Email</label>
							</div>
							<div class='field-body'>
								<div class='field'>
									<div class='control is-expanded'>
										<input class='input' type='email' name='email' id='email' placeholder='Email' value='{{old('email')}}' required/>
									</div>
									@if($errors->has('email'))
									<p class='help is-danger'>
										{{ $errors->first('email') }}
									</p>
									@endif
								</div>
								<div class='field'>
									<div class='control is-expanded'>
										<input class='input' type='email' name='email_confirmation' id='email_confirmation' placeholder='Confirmation' value='{{old('email_confirmation')}}' required/>
									</div>
									@if($errors->has('email_confirmation'))
									<p class='help is-danger'>
										{{ $errors->first('email_confirmation') }}
									</p>
									@endif
								</div>
							</div>
						</div>

                        <div class='field is-horizontal'>
							<div class='field-label is-normal'>
								<label for='password' class='label'>Mot de passe</label>
							</div>
							<div class='field-body'>
								<div class='field'>
									<div class='control is-expanded'>
										<input class='input' type='password' name='password' id='password' placeholder='Mot de passe' value='{{old('password')}}' required/>
									</div>
									@if($errors->has('password'))
									<p class='help is-danger'>
										{{ $errors->first('password') }}
									</p>
									@endif
								</div>
								<div class='field'>
									<div class='control is-expanded'>
										<input class='input' type='password' name='password_confirmation' id='password_confirmation' placeholder='Confirmation' value='{{old('password_confirmation')}}' required/>
									</div>
									@if($errors->has('password_confirmation'))
									<p class='help is-danger'>
										{{ $errors->first('password_confirmation') }}
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
                                        <button type="submit" class="button is-primary">S'inscrire</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
