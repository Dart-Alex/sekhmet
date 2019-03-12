<?php

function message($type, $message) {
	session()->flash('message', [
		'type' => $type,
		'content' => $message
	]);
}

function success($message) {
	message('success', $message);
}

function warning($message) {
	message('warning', $message);
}

function danger($message) {
	message('danger', $message);
}

