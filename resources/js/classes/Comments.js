export default class Comments {
	constructor() {
		this.list = {};
	}
	get(id) {
		if(id) return this.list[id];
		else return this.list;
	}
	add(comment) {
		comment["replies"] = [];
		window.Vue.set(this.list, comment.id, comment);
		if(comment.reply_to) this.list[comment.reply_to].replies.push(comment.id);
	}
	delete(comment) {
		this.list[comment.id].replies.forEach(replyId => {
			this.delete(this.list[replyId]);
		});
		if(comment.reply_to) {
			this.list[comment.reply_to].replies.splice(this.list[comment.reply_to].replies.indexOf(comment.id),1);
		}
		window.Vue.delete(this.list, comment.id);
	}
	update(comment) {
		comment['replies'] = this.list[comment.id].replies;
		this.list[comment.id] = comment;
	}
}
