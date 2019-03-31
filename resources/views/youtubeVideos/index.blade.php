@extends('layouts.app')
@section('content')
<div class="container">
	<div class="content">
		<form action="{{route('youtubeVideos.search', ['chan' => $chan->name])}}" method="post">
			@csrf
		<div class='field is-grouped'>
			<div class='control'>
				<input class='input' type='text' name='name' id='name' placeholder='Recherche' value='{{old('name')}}' required/>
			</div>
			<div class="control">
				<input type="submit" class="button is-primary" value="Rechercher"/>
			</div>
			@if ($errors->has('name'))
			<p class='help is-danger'>
				{{ $errors->first('name') }}
			</p>
			@endif
		</div>
		</form>
		<table class='table-responsive'>
			<thead>
				<tr>
					<th>Index</th>
					<th>Vidéo</th>
					<th>Titre</th>
					<th>Par</th>
					<th>Le</th>
				</tr>
			</thead>
			<tbody>
			@foreach ($youtubeVideos as $youtubeVideo)
				<?php $info = $youtubeVideo->getInfo() ?>
				@if($info)
				<tr>
					<td>{{$youtubeVideo->getIndex()}}</td>
					<td>
						<a class="no-modify" href="https://youtu.be/{{$youtubeVideo->yid}}" target="__blank">
							<img src="{{$info->snippet->thumbnails->default->url}}" height="{{$info->snippet->thumbnails->default->height}}" width="{{$info->snippet->thumbnails->default->width}}"/>
						</a>
					</td>
					<td>
						<a href="https://youtu.be/{{$youtubeVideo->yid}}" target="__blank">
							{{$info->snippet->title}} [{{$youtubeVideo->getDuration()}}]
						</a>
					</td>
					<td><a href={{route('youtubeVideos.indexName', ['chan' => $chan, 'name' => $youtubeVideo->displayName()])}}>{{$youtubeVideo->displayName()}}</a></td>
					<td>{{$youtubeVideo->created_at->isoFormat('LLLL')}}</td>
				</tr>
				@endif
			@endforeach
			</tbody>
		</table>
	</div>

	{{$youtubeVideos->links()}}
</div>
@endsection
