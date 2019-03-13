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
	methods: {
		addUser(user) {
			if(!user.guest) {
				if(!this.users.includes(user.name)) {
					this.users.push(user.name);
				}
			}
		}
	},
	created() {
		this.channel.listen('Login', e => {})
			.here(users => {
				users.forEach(user => {
					this.addUser(user);
				});
			})
			.joining(user => {
				this.addUser(user);
			})
			.leaving(user => {
				this.addUser(user);
			})
	}
}
</script>

