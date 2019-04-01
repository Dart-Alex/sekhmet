@extends('layouts.app')

@section('content')
<div>
    <div class="columns is-marginless is-centered">
        <div class="column is-5">
            <div class="card">
                <header class="card-header">
                    <p class="card-header-title">{{ __('Verify Your Email Address') }}</p>
                </header>

                <div class="card-content">
                    @if (session('resent'))
                            {{ success(__('A fresh verification link has been sent to your email address.')) }}
                    @endif

                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }}, <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
