@extends('layouts.app')
@section('content')
<div class="box content">
	<form action="{{route('chans.update',['chan' => $chan->name])}}" method="POST">
		@csrf
		@method('PATCH')
		<h3>Configuration</h3>
		<div class='field'>
			<label for='name' class='label'>Nom du chan</label>
			<div class='control has-icons-left'>
				<input class='input' type='text' name='name' id='name' value='{{old('name', $chan->name)}}' required {{((auth()->user()->isAdmin())?'':'readonly')}}/>
				<span class="icon is-small is-left"><i class="fas fa-hashtag"></i></span>
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
				<textarea class='textarea' name='description' id='description' required>{{old('description', $chan->description)}}</textarea>
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
					<input type="checkbox" name="hidden" {{(old('hidden', $chan->hidden)?'checked':'')}}>
					Cacher le chan
				</label>
			</div>
		</div>

		<h3>Youtube</h3>
		<div class="field">
			<div class="control">
				<label class="checkbox">
					<input type="checkbox" name="config_youtube_active" {{(old('config_youtube_active', $chan->config_youtube_active)?'checked':'')}}>
					Actif
				</label>
			</div>
		</div>
		<div class='field'>
			<label for='config_youtube_timer' class='label'>Timer (en secondes)</label>
			<div class='control'>
				<input class='input' type='number' name='config_youtube_timer' id='config_youtube_timer' value='{{old('config_youtube_timer', $chan->config_youtube_timer)}}'/>
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
					<input type="checkbox" name="config_spam_active" {{(old('config_spam_active', $chan->config_spam_active)?'checked':'')}}>
					Actif
				</label>
			</div>
		</div>
		<div class='field'>
			<label for='config_spam_timer' class='label'>Timer (en secondes)</label>
			<div class='control'>
				<input class='input' type='number' name='config_spam_timer' id='config_spam_timer' value='{{old('config_spam_timer', $chan->config_spam_timer)}}'/>
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
					<input type="checkbox" name="config_event_active" {{(old('config_event_active', $chan->config_event_active)?'checked':'')}}>
					Actif
				</label>
			</div>
		</div>
		<div class='field'>
			<label for='config_event_timer' class='label'></label>
			<div class='control'>
				<input class='input' type='number' name='config_event_timer' id='config_event_timer' value='{{old('config_event_timer', $chan->config_event_timer)}}'/>
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
