<template>
	<div class="content" v-if="notEmpty">
		<p>Utilisateurs connectés :</p>
		<ul>
			<li v-for="user in users" :key="user" v-text="user"></li>
		</ul>
	</div>
</template>

<script>
export default {
	data() {
		return {
			users: []
		}
	},
	computed: {
		channel() {
			return window.Echo.join('logged-in');
		},
		notEmpty() {
			return this.users.length > 0;
		}
	},
	created() {
		this.channel.listen('Login', e => {})
			.here(users => {
				users.forEach(user => {
					if(!user.guest) this.users.push(user.name);
				});
			})
			.joining(user => {
				if(!user.guest) this.users.push(user.name);
			})
			.leaving(user => {
				if(!user.guest) this.users.splice(this.users.indexOf(user.name), 1);
			})
	}
}
</script>

