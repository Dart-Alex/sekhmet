@extends('layouts.app')

@section('content')
    <div class="content">
		<ul><li><h2><a href="{{route('chans.index')}}">Chans</a></h2></li></ul>
		<h3>Page d'accueil de Sekhmet</h3>
		<h4>Fait</h4>
		<ul>
			<li>Enregistrement utilisateurs, gestion des accès, validation des pseudos par le bot via la page profil</li>
			<li>Administration des canaux</li>
			<li>Bot et son api</li>
			<li>Liste des commandes irc via !aide</li>
			<li>Events pleinement fonctionnels (ajout/modification/spam du bot configurable/inscription/commentaires)</li>
			<li>Affichage du titre d'une page lors de l'envoi d'un url sur irc</li>
			<li>Recherche google via le bot</li>
			<li>Liste des vidéos youtube par chan et par utilisateur</li>
			<li>Formulaire de contact administration du site/d'un canal</li>
		</ul>
		<h4>A venir</h4>
		<ul>
			<li>Design</li>
		</ul>
    </div>
@endsection
