@extends('layouts.app')
@section('content')
<div class="content">
	@foreach ($posts as $post)
		<div class="box">
			<h2><a href="{{route('posts.show', ["chan" => $chan->name, "post" => $post->id])}}">{{ $post->name }}</a></h2>
			<p>Aura lieu {{ $post->date->diffForHumans() }} ({{$post->date->isoFormat('LLLL')}})</p>
			<p>{{$post->content}}</p>
		</div>
	@endforeach
</div>
@endsection
