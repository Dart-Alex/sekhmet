@extends('layouts.app')
@section('content')
<div class="box content">
	<form action="{{route('chans.store')}}" method="POST">
		@csrf
		<h3>Configuration</h3>
		<div class='field'>
			<label for='name' class='label'>Nom du chan (sans #)</label>
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
			<label for='description' class='label'>Description du chan</label>
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

		<h3>Youtube</h3>
		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input type="checkbox" name="config_youtube_active">
					Actif
				</label>
			</div>
		</div>
		<div class='field'>
			<label for='config_youtube_timer' class='label'>Timer (en secondes)</label>
			<div class='control'>
				<input class='input' type='number' name='config_youtube_timer' id='config_youtube_timer' value='{{old('config_youtube_timer', 1800)}}'/>
			</div>
			@if ($errors->has('config_youtube_timer'))
			<p class='help is-danger'>
				{{ $errors->first('config_youtube_timer') }}
			</p>
			@endif
		</div>
		<h3>Spam</h3>
		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input type="checkbox" name="config_spam_active">
					Actif
				</label>
			</div>
		</div>
		<div class='field'>
			<label for='config_spam_timer' class='label'>Timer (en secondes)</label>
			<div class='control'>
				<input class='input' type='number' name='config_spam_timer' id='config_spam_timer' value='{{old('config_spam_timer', 3600)}}'/>
			</div>
			@if ($errors->has('config_spam_timer'))
			<p class='help is-danger'>
				{{ $errors->first('config_spam_timer', 3600) }}
			</p>
			@endif
		</div>
		<h3>Event</h3>
		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input type="checkbox" name="config_event_active">
					Actif
				</label>
			</div>
		</div>
		<div class='field'>
			<label for='config_event_timer' class='label'></label>
			<div class='control'>
				<input class='input' type='number' name='config_event_timer' id='config_event_timer' value='{{old('config_event_timer', 3600)}}'/>
			</div>
			@if ($errors->has('config_event_timer'))
			<p class='help is-danger'>
				{{ $errors->first('config_event_timer') }}
			</p>
			@endif
		</div>

		<div class="field">
			<div class="control">
				<input type="submit" class="button is-primary"/>
			</div>
		</div>
	</form>
</div>
@endsection
