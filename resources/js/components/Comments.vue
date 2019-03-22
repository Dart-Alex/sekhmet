<template>
	<div class="box">
		<message v-if="form.message.any()" :type="form.message.type" :content="form.message.content"></message>
		<h2>Commentaires</h2>
		<comment v-for="(comment, index) in rootComments" :key="index" :id="index"></comment>
		<div class="box">
			<form :method="form.method" :action="form.action" @submit.prevent="onSubmit" @keydown="form.errors.clear($event.target.name)">
				<div class='field'>
					<label for='name' class='label'>Auteur</label>
					<div class='control'>
						<input class='input' type='text' name='name' id='name' v-model="form.name"/>
					</div>
					<span class='help is-danger' v-if="form.errors.has('name')" v-text="form.errors.get('name')"></span>
				</div>
				<div class='field'>
					<label for='content' class='label'>Message</label>
					<div class='control'>
						<textarea class='textarea' name='content' id='content' v-model="form.content"></textarea>
					</div>
					<span class='help is-danger' v-if="form.errors.has('content')" v-text="form.errors.get('content')"></span>
				</div>
				<div class='field is-grouped'>
					<div class="control">
						<input type="submit" class="button is-primary" value="Envoyer" :disabled="form.errors.any()"/>
					</div>
					<div class="control">
						<input type="reset" class="button" value="Effacer" @click.prevent="form.reset"/>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import Comment from "./Comment.vue";
import Form from "../classes/Form.js";
import Comments from "../classes/Comments.js";
export default {
	data() {
		return {
			comments: new Comments(),
			user: this.$parent.user,
			form: new Form({
				name: '',
				content: '',
				post_id: this.id,
				action: '/api/comments',
				method: 'POST'
			})
		}
	},
	computed: {
		channel() {
			return window.Echo.channel('comments-'+this.id);
		},
		rootComments() {
			let rootComments = {};
			for(let index in this.comments.get()) {
				if(this.comments.get(index).reply_to === null) {
					window.Vue.set(rootComments, index, this.comments.get(index));
				}
			}
			return rootComments;
		}
	},
	methods: {
		onSubmit() {
			this.form.submit().then(response => {
				this.comments.add(response.data.comment);
			});
		}
	},
	props: ['id'],
	components: {
		comment: Comment
	},
	mounted() {
		this.form.remember('name', ((this.user.guest)?'Anonyme':this.user.name));
		this.$get('/api/comments/'+this.id).then(response => {
			response.data.forEach(comment => {
				this.comments.add(comment);
			});
			console.log(this.comments.get(9));
		});
		this.channel
			.listen('CommentAdded', e => {
				this.comments.add(e.comment);
			})
			.listen('CommentUpdated', e => {
				this.comments.update(e.comment);
			})
			.listen('CommentDeleted', e => {
				this.comments.delete(e.comment);
			})
		;

	}
}
</script>

<style>

</style>
