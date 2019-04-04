@extends('layouts.app')
@section('content')
<div class="content">
	<div class="card">
		<header class="card-header">
			<div class="card-header-title is-size-3">
				{{ $chan->displayName() }}
			</div>
			@can('update', $chan)
			<a href="{{route('chans.edit', ['chan' => $chan->name])}}" class="card-header-icon has-text-warning">
				<span class="icon">
					<i class="fas fa-edit"></i>
				</span>
			</a>
			@endcan
			@can('delete', $chan)
			<a title="Supprimer" class="card-header-icon has-text-danger" onclick="event.preventDefault();document.getElementById('form-chan-delete').submit()">
				<span class="icon">
					<i class="fas fa-trash"></i>
				</span>
			</a>
			<form id="form-chan-delete" action="{{route('chans.destroy', ['chan' => $chan->name])}}" method="POST">
				@csrf
				@method('DELETE')
			</form>
			@endcan
		</header>
		<div class="card-content">
			{{ $chan->description }}
		</div>
		<footer class="card-footer">
			<a title="Événements" class="card-footer-item" href="{{route('posts.index', ['chan' => $chan->name])}}">
				<span class="icon"><i class="fas fa-calendar-alt"></i></span>
				<span class="is-hidden-mobile">&Eacute;v&eacute;nements</span>
				<span>({{$chan->countNewPosts()}})</span>
			</a>
			<a title="Youtube" class="card-footer-item" href="{{route('youtubeVideos.index', ['chan' => $chan->name])}}">
				<span class="icon"><i class="fab fa-youtube"></i></span>
				<span class="is-hidden-mobile">Youtube</span>
				<span>({{$chan->countYoutubeVideos()}})</span>
			</a>
			<a title="Contact" class="card-footer-item" href="{{route('contact')}}?chan={{$chan->name}}">
				<span class="icon"><i class="fas fa-envelope"></i></span>
				<span class="is-hidden-mobile">Contact</span>
			</a>
		</footer>
	</div>

	</div>
	<div class="panel">
		<p class="panel-heading">
			Utilisateurs
		</p>
		@if($chan->chanUsers->count() > 0)
		@foreach($chan->chanUsers->sortByDesc('admin') as $chanUser)
		<div class="panel-block">
			<span class="panel-icon">
				@if($chanUser->admin)
				<i class="fas fa-at"></i>
				@endif
			</span>
			{{$chanUser->user->name}}
			({{join(', ', $chanUser->user->ircNames->pluck('name')->toArray())}})
			<span class="is-inline-flex is-pulled-right">
				@can('update', [$chanUser, $chan])
				<a title="{{($chanUser->admin?'Rendre utilisateur':'Rendre admin')}}" href="{{route('chanUsers.update', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" onclick="event.preventDefault();document.getElementById('update-form-{{$chanUser->id}}').submit();">
					<span class="icon"><i class="fas fa-chevron-{{($chanUser->admin?'down':'up')}}"></i></span>
				</a>
				<form id='update-form-{{$chanUser->id}}' action="{{route('chanUsers.update', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" method="post">
					@csrf
					@method('PATCH')
				</form>
				@endcan
				@can('delete', [$chanUser, $chan])
				<a title="Supprimer du chan" href="{{route('chanUsers.destroy', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" onclick="event.preventDefault();document.getElementById('delete-form-{{$chanUser->id}}').submit();">
					<span class="icon"><i class="fas fa-trash"></i></span>
				</a>
				<form id='delete-form-{{$chanUser->id}}' action="{{route('chanUsers.destroy', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" method="post">
					@csrf
					@method('DELETE')
				</form>
				@endcan
			</span>
		</div>
		@endforeach
		@endif
		@can('update', $chan)
		<div class="panel-block">
			<form action='{{route('chanUsers.store', ['chan' => $chan->name])}}' method='post'>
				@csrf
				<div class="field is-grouped">
					<div class="control">
						<div class="select">
							<select name="user_id" required>
								@foreach($users->sortBy('name') as $user)
								@if(!$chan->chanUsers->contains('user_id', $user->id))
								<option value={{$user->id}}>{{$user->name}} </option>
								@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="control">
						<input type="submit" class="button is-primary" value="+">
					</div>
				</div>
			</form>
		</div>
		@endcan
		@can('join', $chan)
		<div class="panel-block">
			<a class="button is-primary is-outlined is-fullwidth" onclick="event.preventDefault();document.getElementById('form-join').submit()">
				Rejoindre le chan
			</a>
			<form id="form-join" action='{{route('chanUsers.store', ['chan' => $chan->name])}}' method='POST'>
				@csrf
			</form>
		</div>
		@endcan
		@can('part', $chan)
		<div class="panel-block">
			<a class="button is-danger is-outlined is-fullwidth" onclick="event.preventDefault();document.getElementById('form-part').submit()">
				Partir du chan
			</a>
			<form id="form-part" action="{{route('chanUsers.destroy', ['chan' => $chan->name, 'chanUser' => $chan->chanUser(auth()->user())])}}" method="post">
				@csrf
				@method('DELETE')
			</form>
		</div>
		@endcan
	</div>
</div>
@endsection
