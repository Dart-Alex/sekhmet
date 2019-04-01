<div class="post-bloc">
	@if($post->hasImage())
	<div class="post-cover" style="background-image:url('{!!$post->getImage()!!}');">
		<a href="{{route('posts.show', ["chan" => $chan->name, "post" => $post->id])}}"></a>
	</div>
	@endif
	<div class="post-extract">

		<p class="title is-4 is-spaced">
			<a href="{{route('posts.show', ["chan" => $chan->name, "post" => $post->id])}}">{{ $post->name }}</a>
		</p>
		<p class="subtitle is-6 has-text-grey-light is-spaced">
			{{($post->date <= now())?'A eu lieu':'Aura lieu'}} {{ $post->date->diffForHumans() }} ({{$post->date->isoFormat('LLLL')}})
		</p>
		<div class="extract">
			{!!Str::words(strip_tags($post->content), '100', '...')!!}
		</div>
		<div class="bottom">
			<div class="left">
				@if($post->comments_allowed)
				<span title="Commentaires" class="icon"><i class="fas fa-comments">&nbsp;{{$post->comments->count()}}</i></span>
				&nbsp;
				@endif
				<span title="Inscrits" class="icon"><i class="fas fa-user">&nbsp;{{$post->postSubscribers->count()}}</i></span>

			</div>
			<div class="right">
				<a href="{{route('posts.show', ["chan" => $chan->name, "post" => $post->id])}}">Lire la suite...</a>
			</div>
		</div>
	</div>
</div>
