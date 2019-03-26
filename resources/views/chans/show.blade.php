@extends('layouts.app')
@section('content')
<div class="content">

	<h1>{{ $chan->displayName() }}</h1>
	<p>{{ $chan->description }}</p>
	<ul>
		<li><a href="{{route('posts.index', ['chan' => $chan->name])}}">Evenements</a></li>
		<li><a href="{{route('youtubeVideos.index', ['chan' => $chan->name])}}">Youtube</a></li>
		<li><a href="">Contacter les administrateurs</a></li>
	</ul>
	<div class='field is-grouped'>
		@can('join', $chan)
		<div class="control">
			<form action='{{route('chanUsers.store', ['chan' => $chan->name])}}' method='POST'>
				@csrf
				<input type='submit' class='button is-primary' value='Rejoindre le chan'/>
			</form>
		</div>
		@endcan
		@can('part', $chan)
		<div class='control'>
			<form action="{{route('chanUsers.destroy', ['chan' => $chan->name, 'chanUser' => $chan->chanUser(auth()->user())])}}" method="post">
				@csrf
				@method('DELETE')
				<input type='submit' class='button is-danger' value='Partir du chan'/>
			</form>
		</div>
		@endcan
		@can('update', $chan)
		<div class="control">
			<a href="{{route('chans.edit', ['chan' => $chan->name])}}" class="button is-warning">Modifier le chan</a>
		</div>
		@endcan
		@can('delete', $chan)
		<form action="{{route('chans.destroy', ['chan' => $chan->name])}}" method="POST">
			@csrf
			@method('DELETE')
			<div class="control">
				<input type="submit" class="button is-danger" value="Supprimer le chan"/>
			</div>
		</form>
		@endcan
	</div>
	<h2>Utilisateurs</h2>
	<ul>
		@foreach($chan->chanUsers->sortByDesc('admin') as $chanUser)
		<li>
			{{$chanUser->admin?'@':''}}
			{{$chanUser->user->name}}
			({{join(', ', $chanUser->user->ircNames->pluck('name')->toArray())}})
			<span style='display:inline-flex;'>
				@can('update', [$chanUser, $chan])
				<a title="{{($chanUser->admin?'Rendre utilisateur':'Rendre admin')}}" class="fas fa-chevron-{{($chanUser->admin?'down':'up')}}" href="{{route('chanUsers.update', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" onclick="event.preventDefault();document.getElementById('update-form-{{$chanUser->id}}').submit();"></a>
				<form id='update-form-{{$chanUser->id}}' action="{{route('chanUsers.update', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" method="post">
					@csrf
					@method('PATCH')
				</form>
				@endcan
				@can('delete', [$chanUser, $chan])
				<a title="Supprimer du chan" class="fas fa-trash" href="{{route('chanUsers.destroy', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" onclick="event.preventDefault();document.getElementById('delete-form-{{$chanUser->id}}').submit();"></a>
				<form id='delete-form-{{$chanUser->id}}' action="{{route('chanUsers.destroy', ['chan' => $chan->name, 'chanUser' => $chanUser->id])}}" method="post">
					@csrf
					@method('DELETE')
				</form>
				@endcan
			</span>
		</li>
		@endforeach
		@can('update', $chan)
		<li>
			<form action='{{route('chanUsers.store', ['chan' => $chan->name])}}' method='post'>
				@csrf
				<div class="field is-grouped">
					<div class="control">
						<div class="select">
							<select name="user_id" required>
								@foreach($users->sortBy('name') as $user)
								@if(!$user->hasChan($chan))
								<option value={{$user->id}}>{{$user->name}} </option>
								@endif
								@endforeach
							</select>
						</div>
					</div>
					<div class="control">
						<input type="submit" class="button is-primary" value="Ajouter">
					</div>
				</div>
			</form>
		</li>
		@endcan
	</ul>
</div>
@endsection
