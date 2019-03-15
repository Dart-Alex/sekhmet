
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

let userMeta = document.head.querySelector('meta[name="user"]');
let user;
if (userMeta) {
	user = JSON.parse(userMeta.content);
	if(user == null) {
		user = {"guest": true};
	}
	else {
		user["guest"] = false;
	}
}

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import Message from './components/Message.vue';
import Comments from './components/Comments.vue';
Vue.prototype.$http = axios;

const app = new Vue({
	el: '#app',
	data: {
		user: user
	},
	components: {
		"message": Message,
		"comments": Comments
	}
});



document.addEventListener('DOMContentLoaded', function () {

	// Confirmation field validation

	const $inputTexts = Array.prototype.slice.call(document.querySelectorAll('form input'), 0);

	if ($inputTexts.length > 0) {
		function validateConfirmation($el, $elConfirm) {
			if($el.value != $elConfirm.value) {
				$elConfirm.setCustomValidity("Les champs ne sont pas identiques.");
			} else {
				$elConfirm.setCustomValidity('');
			}
		}
		$inputTexts.forEach($elConfirm => {
			let $position = $elConfirm.name.search('_confirmation');
			if($position != -1) {
				let name = $elConfirm.name.substring(0, $position);
				let $el = document.querySelectorAll('input[name="'+name+'"')[0];
				$el.addEventListener('change', () => {
					validateConfirmation($el, $elConfirm);
				});
				$elConfirm.addEventListener('keyup', () => {
					validateConfirmation($el, $elConfirm);
				});
			}
		})
	}

	// Bulma NavBar Burger Script
    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Check if there are any navbar burgers
    if ($navbarBurgers.length > 0) {

        // Add a click event on each of them
        $navbarBurgers.forEach(function ($el) {
            $el.addEventListener('click', function () {

                // Get the target from the "data-target" attribute
                let target = $el.dataset.target;
                let $target = document.getElementById(target);

                // Toggle the class on both the "navbar-burger" and the "navbar-menu"
                $el.classList.toggle('is-active');
                $target.classList.toggle('is-active');

            });
        });
	}

	const $hasDropDown = Array.prototype.slice.call(document.querySelectorAll('.has-dropdown'), 0);

	if($hasDropDown.length > 0) {
		$hasDropDown.forEach($el => {
			$el.addEventListener('click', () => {
				let target = $el.dataset.target;
				let $target = document.getElementById(target);

				$el.classList.toggle('is-active');
				$target.classList.toggle('is-active');
			});
		});
	}

});



require('./bulma-extensions');
