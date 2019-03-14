@extends('layouts.app')
@section('content')
<div class="content">
	<h1>{{$post->name}}</h1>
	<p>Aura lieu {{ $post->date->diffForHumans() }} ({{$post->date->isoFormat('LLLL')}})</p>
	<p>{{$post->content}}</p>
	<div id="comments">
		<comments id="{{$post->id}}"></comments>
	</div>
</div>
@endsection
@section('scripts')
<script src='{{mix("js/comments.js")}}'></script>
@endsection
