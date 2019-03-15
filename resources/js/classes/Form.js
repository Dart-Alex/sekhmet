
import Errors from "./Errors.js";
import Message from "./Message.js";
import Axios from "axios";
export default class Form {
	constructor(data) {
		this.originalData = data;
		for(let field in data) {
			this[field] = data[field];
		}
		delete this.originalData['action'];
		delete this.originalData['method'];
		this.errors = new Errors();
		this.message = new Message();
	}
	data() {
		let data = {};
		for (let field in this.originalData) {
			data[field] = this[field];
		}
		return data;
	}
	remember(field, content) {
		this.originalData[field] = content;
		this[field] = content;
	}
	reset() {
		for (let field in this.originalData) {
			this[field] = this.originalData[field];
		}
	}
	submit() {
		return new Promise((resolve, reject) => {
			let method = 'post';
			let action = this.action;
			let data = this.data();
			data['_method'] = this.method;
			if(this.method == "GET") {
				delete data['_method'];
				method = 'get';
			}

			Axios[method](action, data)
					.then(response => {
						this.onSuccess(response);

						resolve(response);
					})
					.catch(error => {
						this.onFail(error);

						reject(error);
					})
				;
		});


	}
	onSuccess(response) {
		this.message.send(response.data.message);
		this.errors.clear();
		this.reset();
	}
	onFail(error) {
		if(error.response) {
			this.errors.record(error.response.data.errors);
			let message = {
				type:'danger',
				content:error.response.data.message
			}
			this.message.send(message)
		}
	}
}
