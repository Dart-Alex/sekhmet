@extends('layouts.app')
@section('content')
<div class="content">

		@foreach($users as $user)
		<div class="box">
			<div style="float:right;" class="field is-grouped">
				<div class="control">
					<a href="{{ route('users.edit', ['user' => $user->id]) }}" class="button is-warning">Editer</a>
				</div>
				<div class="control">
					<form action="{{ route('users.destroy', ['user' => $user->id])}}" method="POST">
						@csrf
						@method("DELETE")
						<input type="submit" class="button is-danger" value="Supprimer"/>
					</form>
				</div>
			</div>
			<h2><a href="{{ route('users.edit', ['user' => $user->id]) }}">{{ $user->name }}</a></h2>
			<p>Email : {{$user->email}}</p>
			<p>Admin : {{($user->isAdmin()?'Oui':'Non')}}</p>
			<p>Créé le : {{$user->created_at}}</p>
			<p>Mis à jour le : {{$user->updated_at}}</p>
			<p>Pseudos irc :
				<ul>
					@foreach($user->ircNames as $ircName)
					<li>{{$ircName->name}}</li>
					@endforeach
				</ul>
			</p>
			<p>Salons :
				<ul>
					@foreach($user->chanUsers as $chanUser)
					<li>{{$chanUser->chan->displayName()}} : {{(($chanUser->admin)?'Admin':'Pas admin')}}</li>
					@endforeach
				</ul>
			</p>
		</div>
		@endforeach

		{{$users->links()}}


</div>
@endsection
