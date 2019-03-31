@extends('layouts.app')
@section('content')
<div class="content">

	<h1>{{ $chan->displayName() }}</h1>
	<p>{{ $chan->description }}</p>
	<div class="container">
		<a class="button is-info" href="{{route('posts.index', ['chan' => $chan->name])}}">
			<span class="icon"><i class="fas fa-calendar-alt"></i></span>
			<span>&Eacute;v&eacute;nements</span>
		</a>
		<a class="button is-info" href="{{route('youtubeVideos.index', ['chan' => $chan->name])}}">
			<span class="icon"><i class="fab fa-youtube"></i></span>
			<span>Youtube</span>
		</a>
		<a class="button is-info" href="{{route('contact')}}?chan={{$chan->name}}">
			<span class="icon"><i class="fas fa-envelope"></i></span>
			<span>Contacter les administrateurs</span>
		</a>
	</div>
	<br/>
	<div class="container">
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
	</div>
	@if($chan->chanUsers->count() > 0)
	<h2>Utilisateurs</h2>
	<ul class="fa-ul">
		@foreach($chan->chanUsers->sortByDesc('admin') as $chanUser)
		<li {!!($chanUser->admin?'style="list-style-type:none;"':'')!!}>
			{!!$chanUser->admin?'<span class="fa-li"><i class="fas fa-at"></i></span>':''!!}
			<span>
				{{$chanUser->user->name}}
				({{join(', ', $chanUser->user->ircNames->pluck('name')->toArray())}})
			</span>
			<span style="display:inline-flex;">
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
								@if(!$chan->chanUsers->contains('user_id', $user->id))
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
	@endif
</div>
@endsection
