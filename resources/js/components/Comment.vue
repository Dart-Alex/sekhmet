<template>
	<div class="comment">
		<message v-if="deleteForm.message.any()" :type="deleteForm.message.type" :content="deleteForm.message.content"></message>
		<div style="margin-bottom:24px;" v-if="!editing">
			<span class="comment-buttons">
				<a v-if='canModify' class='fas fa-trash' title='Supprimer' @click='onDelete'></a>
				<a v-if='canModify' class='fas fa-edit' title='Editer' @click='toggleEdit'></a>
			</span>
			<p>Par {{comment.name}}</p>
			<p>{{createdAt}}</p>
			<nl2br tag="p" :text='comment.content'></nl2br>
			<p v-if="comment.created_at != comment.updated_at">{{updatedAt}}</p>
		</div>
		<message v-if="editForm.message.any()" :type="editForm.message.type" :content="editForm.message.content"></message>
		<form style="margin-bottom:24px;" v-if="editing" :method='editForm.method' :action='editForm.action' @submit.prevent='onModify' @keydown='editForm.errors.clear($event.target.name)'>
			<div class='field'>
				<label for='name' class='label'>Auteur</label>
				<div class='control'>
					<input class='input' type='text' name='name' id='name' v-model='editForm.name'/>
				</div>
				<span class='help is-danger' v-if="editForm.errors.has('name')" v-text="editForm.errors.get('name')"></span>
			</div>
			<div class='field'>
				<label for='content' class='label'>Message</label>
				<div class='control'>
					<textarea class='textarea' name='content' id='content' v-model='editForm.content'></textarea>
				</div>
				<span class='help is-danger' v-if="editForm.errors.has('content')" v-text="editForm.errors.get('content')"></span>
			</div>
			<div class='field is-grouped'>
				<div class='control'>
					<input type='submit' class='button is-primary' value='Modifier' :disabled='editForm.errors.any()'/>
				</div>
				<div class='control'>
					<input type='reset' class='button' value='Annuler' @click.prevent='toggleEdit'/>
				</div>
			</div>
		</form>

		<comment v-for="index in comment.replies" :key="index" :id="index"></comment>
		<a v-if="!replying" class="button is-primary" @click="toggleReply">Répondre</a>
		<div v-if="replying" class="comment">
			<message v-if="replyForm.message.any()" :type="replyForm.message.type" :content="replyForm.message.content"></message>
			<form :method='replyForm.method' :action='replyForm.action' @submit.prevent='onReply' @keydown='replyForm.errors.clear($event.target.name)'>
				<div class='field'>
					<label for='name' class='label'>Auteur</label>
					<div class='control'>
						<input class='input' type='text' name='name' id='name' v-model='replyForm.name'/>
					</div>
					<span class='help is-danger' v-if="replyForm.errors.has('name')" v-text="replyForm.errors.get('name')"></span>
				</div>
				<div class='field'>
					<label for='content' class='label'>Message</label>
					<div class='control'>
						<textarea class='textarea' name='content' id='content' v-model='replyForm.content'></textarea>
					</div>
					<span class='help is-danger' v-if="replyForm.errors.has('content')" v-text="replyForm.errors.get('content')"></span>
				</div>
				<div class='field is-grouped is-grouped-centered'>
					<div class='control'>
						<input type='submit' class='button is-primary' value='Répondre' :disabled='replyForm.errors.any()'/>
					</div>
					<div class='control'>
						<input type='reset' class='button' value='Annuler' @click.prevent='toggleReply'/>
					</div>
				</div>
			</form>
		</div>
	</div>
</template>

<script>
import Form from "../classes/Form.js";
import dateFormat from "../functions/dateFormat.js";
import Nl2br from 'vue-nl2br';
export default {
	name: "comment",
	props: ['id'],
	components: {
		"nl2br":Nl2br
	},
	data() {
		return {
			user: this.$parent.user,
			comments: this.$parent.comments,
			editing: false,
			replying: false,
			editForm: new Form({
				name: '',
				content: '',
				action: '/api/comments/'+this.id,
				method: 'PATCH'
			}),
			deleteForm: new Form({
				action: '/api/comments/'+this.id,
				method: 'DELETE'
			}),
			replyForm: new Form({
				name: '',
				content: '',
				action: '/api/comments',
				method: 'POST'
			})

		}
	},
	computed: {
		comment() {
			return this.comments.get(this.id);
		},
		canModify() {
			if(this.user.guest) return false;
			if(this.user.admin) return true;
			if(this.user.chan_admin) return true;
			if(this.comment.user_id == this.user.id) return true;
			return false;
		},
		createdAt() {
			return 'Le '+dateFormat(this.comment.created_at);
		},
		updatedAt() {
			return 'Modifié le '+dateFormat(this.comment.updated_at);
		}
	},
	methods: {
		toggleEdit() {
			if(this.editing) this.editForm.reset();
			this.editing = !this.editing;
		},
		toggleReply() {
			if(this.replying) this.replyForm.reset();
			this.replying = !this.replying;
		},
		onDelete() {
			this.deleteForm.submit().then(response => {
				this.comments.delete(response.data.comment);
			});
		},
		onModify() {
			this.editForm.submit().then(response => {
				let comment = response.data.comment;
				this.editForm.remember('name', comment.name);
				this.editForm.remember('content', comment.content);
				this.editing = false;
				this.comments.update(response.data.comment);
			});
		},
		onReply() {
			this.replyForm.submit().then(response => {
				this.replying = false;
				this.comments.add(response.data.comment);
			});
		}
	},
	mounted() {
		this.editForm.remember('name', this.comment.name);
		this.editForm.remember('content', this.comment.content);
		this.replyForm.remember('name', ((this.user.guest)?'Anonyme':this.user.name));
		this.replyForm.remember('post_id', this.comment.post_id);
		this.replyForm.remember('reply_to', this.comment.id);
	}
}
</script>
<style>
	.comment-buttons {
		float:right;
	}
</style>
