<?php

use App\Chan;
use App\Post;
use App\User;

// Home
Breadcrumbs::for('home', function ($trail) {
	$trail->push('Accueil', route('home'));
});

// Home > verification.resend
Breadcrumbs::for('verification.resend', function ($trail) {
	$trail->parent('home');
	$trail->push("Renvoi de l'email de confirmation", route('verification.resend'));
});

// Home > verification.notice
Breadcrumbs::for('verification.notice', function ($trail) {
	$trail->parent('home');
	$trail->push("Veuillez valider votre email", route('verification.notice'));
});

// Home > verification.verify
Breadcrumbs::for('verification.verify', function ($trail, $id) {
	$trail->parent('home');
	$trail->push("Verification", route('verification.verify', ["id" => $id]));
});

// Home > login
Breadcrumbs::for('login', function ($trail) {
	$trail->parent('home');
	$trail->push("Connexion", route('login'));
});

// Home > password.request
Breadcrumbs::for('password.request', function ($trail) {
	$trail->parent('home');
	$trail->push("Demande de renvoi du mot de passe", route('password.request'));
});

// Home > password.reset
Breadcrumbs::for('password.reset', function ($trail, $token) {
	$trail->parent('home');
	$trail->push("Remise à zéro du mot de passe", route('password.reset', ["token" => $token]));
});

// Home > register
Breadcrumbs::for('register', function ($trail) {
	$trail->parent('home');
	$trail->push("Inscription", route('register'));
});

// Home > profile
Breadcrumbs::for('profile', function($trail, User $user) {
	$trail->parent('home');
	$trail->push('Votre profil', route('profile', ['user' => $user->id]));
});

// Home > users.index
BreadCrumbs::for('users.index', function($trail) {
	$trail->parent('home');
	$trail->push('Utilisateurs', route('users.index'));
});

// Home > users.index > {user}
BreadCrumbs::for('users.show', function($trail, User $user) {
	$trail->parent('users.index');
	$trail->push($user->name, route('users.show', ['user' => $user->id]));
});

// Home > users.index > {user} > users.edit
BreadCrumbs::for('users.edit', function($trail, User $user) {
	$trail->parent('users.show', $user);
	$trail->push('Editer', route('users.edit', ['user' => $user->id]));
});

// Home > chans.index
Breadcrumbs::for('chans.index', function($trail) {
	$trail->parent('home');
	$trail->push('Chans', route('chans.index'));
});

// Home > chans.index > chan.create
Breadcrumbs::for('chans.create', function($trail) {
	$trail->parent('chans.index');
	$trail->push("Création d'un chan", route('chans.create'));
});

// Home > chans.index > {chan}
Breadcrumbs::for('chans.show', function($trail, Chan $chan) {
	$trail->parent('chans.index');
	$trail->push($chan->displayName(), route('chans.show', ['chan' => $chan->name]));
});
// Home > chans.index > {chan} > chans.edit
Breadcrumbs::for('chans.edit', function($trail, Chan $chan) {
	$trail->parent('chans.show', $chan);
	$trail->push('Editer le chan', route('chans.edit', ['chan' => $chan->name]));
});

// Home > chans.index > {chan} > posts.index
Breadcrumbs::for('posts.index', function($trail, Chan $chan) {
	$trail->parent('chans.show', $chan);
	$trail->push("Événements", route('posts.index', ["chan" => $chan->name]));
});

// Home > chans.index > {chan} > posts.index > posts.create
Breadcrumbs::for('posts.create', function($trail, Chan $chan) {
	$trail->parent('posts.index', $chan);
	$trail->push("Création", route('posts.create', ["chan" => $chan->name]));
});

// Home > chans.index > {chan} > posts.index > {post}
Breadcrumbs::for('posts.show', function($trail, Chan $chan, Post $post) {
	$trail->parent('posts.index', $chan);
	$trail->push($post->name, route('posts.show', ["chan" => $chan->name, "post" => $post->id]));
});

// Home > chans.index > {chan} > posts.index > {post} > posts.edit
Breadcrumbs::for('posts.edit', function($trail, Chan $chan, Post $post) {
	$trail->parent('post.show', $chan, $post);
	$trail->push("Éditer", route('posts.edit', ["chan" => $chan->name, "post" => $post->id]));
});
