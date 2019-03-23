@extends('layouts.app')
@section('content')
<div class="box content">
	<form action="{{route('posts.store', ['chan' => $chan->name])}}" method="post">
		@csrf
		<div class='field'>
			<label for='name' class='label'>Titre</label>
			<div class='control'>
				<input class='input' type='text' name='name' id='name' placeholder="Titre de l'event" value='{{old('name')}}' required/>
			</div>
			@if ($errors->has('name'))
			<p class='help is-danger'>
				{{ $errors->first('name') }}
			</p>
			@endif
		</div>
		<div class='field'>
			<label for='date' class='label'>Date</label>
			<div class='control'>
				<input class='input' type='datetime-local' name='date' id='date' value='{{old('date', now()->format('Y-m-d\TH:i'))}}' required/>
			</div>
			@if ($errors->has('date'))
			<p class='help is-danger'>
				{{ $errors->first('date') }}
			</p>
			@endif
		</div>
		<div class='field'>
			<label for='content' class='label'>Description</label>
			<div class='control'>
				<textarea class='textarea' name='content' id='content' placeholder='Description'>{{old('content')}}</textarea>
			</div>
			@if ($errors->has('content'))
			<p class='help is-danger'>
				{{ $errors->first('content') }}
			</p>
			@endif
		</div>
		<div class='field'>
			<div class='control'>
				<label class='checkbox'>
					<input type='checkbox' name='comments_allowed' {{(old('comments_allowed')?'checked':'')}}/>
					Autoriser les commentaires
				</label>
			</div>
		</div>
		<div class="field">
			<div class="control">
				<input type="submit" value='Envoyer' class="button is-primary">
			</div>
		</div>
	</form>
</div>
@endsection

@section('scripts')
<script src='https://cloud.tinymce.com/5/tinymce.min.js?apiKey={{env('TINYMCE_KEY', 'TINYMCE_KEY_NOT_SET')}}'></script>
<script src="{{ mix('js/tinyMCE.js') }}"></script>
@endsection
