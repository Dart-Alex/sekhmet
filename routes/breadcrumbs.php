<?php

use App\Chan;

// Home
Breadcrumbs::for('home', function ($trail) {
	$trail->push('Accueil', route('home'));
});

// Home > {chan}
BreadCrumbs::for('chans.show', function($trail, Chan $chan) {
	$trail->parent('home');
	$trail->push($chan->displayName(), route('chans.show', ['chan' => $chan->name]));
});

// Home > create
BreadCrumbs::for('chans.create', function($trail) {
	$trail->parent('home');
	$trail->push("Création d'un chan", route('chans.create'));
});
