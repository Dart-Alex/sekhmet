@extends('layouts.app')
@section('content')
<div class="content">

		@foreach($users as $user)
		<div class="box">
			<p style="float:right;"><a href="{{ route('users.edit', ['user' => $user->id]) }}" class="button is-warning">Editer</a></p>
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


</div>
@endsection
