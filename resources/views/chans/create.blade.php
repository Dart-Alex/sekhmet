@extends('layouts.app')
@section('content')
<div class="box">
	<form action="{{route('chans.store')}}" method="POST">
		@csrf

		<div class='field'>
			<label for='name' class='label'>Nom du chan (sans #):</label>
			<div class='control'>
				<input class='input' type='text' name='name' id='name' value='{{old('name')}}' required/>
			</div>
			@if ($errors->has('name'))
			<p class='help is-danger'>
				{{ $errors->first('name') }}
			</p>
			@endif
		</div>

		<div class='field'>
			<label for='description' class='label'>Description du chan:</label>
			<div class='control'>
				<textarea class='textarea' name='description' id='description' required>{{old('description')}}</textarea>
			</div>
			@if ($errors->has('description'))
			<p class='help is-danger'>
				{{ $errors->first('description') }}
			</p>
			@endif
		</div>

		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input type="checkbox" name="hidden">
					Cacher le chan
				</label>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<input type="submit" class="button is-primary"/>
			</div>
		</div>
	</form>
</div>
@endsection
