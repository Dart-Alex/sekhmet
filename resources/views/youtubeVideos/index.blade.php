@extends('layouts.app')
@section('content')
<div class="content">

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
					<a href="https://youtu.be/{{$youtubeVideo->yid}}" target="__blank">
						<img src="{{$info->snippet->thumbnails->default->url}}" height="{{$info->snippet->thumbnails->default->height}}" width="{{$info->snippet->thumbnails->default->width}}"/>
					</a>
				</td>
				<td>{{$info->snippet->title}}</td>
				<td><a href={{route('youtubeVideos.indexName', ['chan' => $chan, 'name' => $youtubeVideo->name])}}>{{$youtubeVideo->name}}</a></td>
				<td>{{$youtubeVideo->created_at->isoFormat('LLLL')}}</td>
			</tr>
			@endif
		@endforeach
		</tbody>
	</table>

	{{$youtubeVideos->links()}}
</div>
@endsection
