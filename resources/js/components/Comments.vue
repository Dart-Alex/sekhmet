<template>
	<div class="box">
		<h2>Commentaires</h2>
		<ul>
			<comment v-for="(comment, index) in comments" v-if="comment.reply_to == null" :key="index" :id="index"></comment>
		</ul>
	</div>
</template>

<script>
import Comment from "./Comment.vue";
export default {
	data() {
		return {
			comments: {},
			user: this.$parent.user
		}
	},
	computed: {

	},
	methods: {
		addedComment(comment) {
			comment["replies"] = [];
			this.comments[comment.id] = comment;
			if(comment.reply_to) this.comments[comment.reply_to].replies.push(comment.id);
			this.$forceUpdate();
		},
		deletedComment(comment) {
			comment.replies.forEach(replyId => {
				this.deleteComment(this.comments[replyId]);
			});
			delete this.comments[comment.id];
		},
		updatedComment(comment) {
			this.comments[comment.id] = comment;
		}
	},
	props: ['id'],
	components: {
		comment: Comment
	},
	mounted() {
		this.$http.get('/api/comments/'+this.id).then(response => {
			response.data.forEach(comment => {
				this.addedComment(comment);
			});
		});
	}
}
</script>

<style>

</style>
