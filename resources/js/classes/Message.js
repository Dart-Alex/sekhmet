export default class Message {
	constructor() {
		this.type = '';
		this.content = '';
	}
	send(message) {
		this.type = message.type;
		this.content = message.content;
		if(this.content == "The given data was invalid.") this.content = "Données fournies incorrectes.";
		setTimeout(() => {
			this.type = '';
			this.content = '';
		}, 2000);
	}
	any() {
		return this.content != '';
	}
}
