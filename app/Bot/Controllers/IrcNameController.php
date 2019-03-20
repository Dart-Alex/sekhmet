<?php

namespace App\Bot\Controllers;

use App\IrcName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class IrcNameController extends Controller
{
    public function confirm($name, $token) {
			if($validated = Cache::get('ircName-validation-token-'.$token)) {
				if(IrcName::where('name', $validated['name'])->exists()) {
					Cache::forget('ircName-validation-token-'.$token);
					Cache::forget('ircName-validation-user-'.$validated['user_id']);
					return [
						'error' => true,
						'message' => 'Le pseudo a déjà été enregistré.'
					];
				}
				$name = strtolower($name);
				if($name != $validated['name']) {
					return [
						'error' => true,
						'message' => 'Le pseudo que vous essayez de confirmer ne correspond pas à votre pseudo actuel.'
					];
				}
				Cache::forget('ircName-validation-token-'.$token);
				Cache::forget('ircName-validation-user-'.$validated['user_id']);
				unset($validated['token']);
				IrcName::create($validated);
				return [
					'error' => false,
					'message' => 'Votre pseudo est validé.'
				];

			}
			else {
				return [
					'error' => true,
					'message' => 'Le code est incorrect, ou la demande de validation a expiré.'
				];
			}
		}
}
