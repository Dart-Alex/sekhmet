@extends('layouts.app')

@section('content')

    <section class="hero is-primary">
        <div class="hero-body">
            <div class="container">
                <h1 class="title">
                    Réinitialisation du mot de passe
                </h1>
            </div>
        </div>
    </section>


    <div class="columns is-marginless is-centered">
        <div class="column is-5">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">Réinitialisation du mot de passe</p>
                </header>

                <div class="card-content">
                    @if (session('status'))
                            {{ success(session('status')) }}
                    @endif

                    <form class="password-reset-form" method="POST" action="{{ route('password.request') }}">

                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">


                        <div class="field is-horizontal">
                            <div class="field-label">
                                <label class="label">Adresse Email</label>
                            </div>

                            <div class="field-body">
                                <div class="field">
                                    <p class="control has-icons-left">
                                        <input class="input" id="email" type="email" name="email" placeholder="Email"
											   value="{{ old('email') }}" required autofocus>
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
                            <div class="field-label">
                                <label class="label">Confirmation du mot de passe</label>
                            </div>

                            <div class="field-body">
                                <div class="field">
                                    <p class="control has-icons-left">
                                        <input class="input" id="password-confirm" type="password" name="password_confirmation" placeholder="Confirmation du mot de passe" required>
										<span class="icon is-small is-left"><i class="fas fa-lock"></i></span>
									</p>
                                </div>
                            </div>
                        </div>


                        <div class="field is-horizontal">
                            <div class="field-label"></div>

                            <div class="field-body">
                                <div class="field is-grouped">
                                    <div class="control">
                                        <button type="submit" class="button is-primary">Réinitialiser le mot de passe </button>
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
