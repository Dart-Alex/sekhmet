@extends('layouts.app')
@section('content')
<div class="box content">
	<form action="{{route('contact.send')}}" method="post">
		@csrf
		<div class="select">
			<select name="chan_id" id="chan_id" required>
				<option {{($chan?'':'selected')}} value="0">Administrateurs du site</option>
				@foreach ($chans as $chanSelect)
				<option {{ (($chan && $chan->id == $chanSelect->id)?'selected':'') }} value="{{$chanSelect->id}}">{{$chanSelect->displayName()}}</option>
				@endforeach
			</select>
		</div>
		<div class='field'>
			<label for='fromName' class='label'>Votre nom</label>
			<div class='control has-icons-left'>
				<input class='input' type='text' name='fromName' id='fromName' placeholder='Votre nom' value='{{old('fromName', (auth()->guest()?'':auth()->user()->name))}}' required/>
				<span class="icon is-small is-left"><i class="fas fa-user"></i></span>
			</div>
			@if ($errors->has('fromName'))
			<p class='help is-danger'>
				{{ $errors->first('fromName') }}
			</p>
			@endif
		</div>
		<div class='field'>
			<label for='from' class='label'>Votre email</label>
			<div class="field is-grouped">
				<div class='control is-expanded has-icons-left'>
					<input class='input' type='email' name='from' id='from' placeholder='Votre email' value='{{old('from', (auth()->guest()?'':auth()->user()->email))}}' required/>
					<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
				</div>
				@if ($errors->has('from'))
				<p class='help is-danger'>
					{{ $errors->first('from') }}
				</p>
				@endif
				<div class='control is-expanded has-icons-left'>
					<input class='input' type='email' name='from_confirmation' id='from_confirmation' placeholder='Confirmation' value='{{old('from_confirmation', (auth()->guest()?'':auth()->user()->email))}}' required/>
					<span class="icon is-small is-left"><i class="fas fa-envelope"></i></span>
				</div>
				@if ($errors->has('from_confirmation'))
				<p class='help is-danger'>
					{{ $errors->first('from_confirmation') }}
				</p>
				@endif
			</div>
		</div>
		<div class='field'>
			<label for='body' class='label'>Message</label>
			<div class='control'>
				<textarea class='textarea' type='text' name='body' id='body' placeholder='Votre message' required>{{old('body')}}</textarea>
			</div>
			@if ($errors->has('body'))
			<p class='help is-danger'>
				{{ $errors->first('body') }}
			</p>
			@endif
		</div>
		<div class="field">
			<label class="checkbox">
				<input type="checkbox" required/>
				J'accepte la <a href="{{route('polconf')}}">politique de confidentialité</a>
			</label>
		</div>
		<div class="field">
			<div class="control">
				<input type="submit" class="button is-primary" value="Envoyer"/>
			</div>
		</div>
	</form>
</div>
@endsection
