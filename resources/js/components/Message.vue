<template>
  <div id="message" :class="classes" v-show="isVisible">
    <button class="delete" @click="hideMessage"></button>
    {{content}}
  </div>
</template>

<script>
export default {
	name: 'message',
	props: ['type', 'content'],
	data() {
		return {
			isVisible: true,
			classes: "notification is-"+this.type
		}
	},
	methods: {
		hideMessage() {
			this.isVisible = false;
		}
	},
	mounted() {
		setTimeout(() => {
			this.hideMessage();
		}, 5000);
	}
};
</script>

<style>
#message {
	position: fixed;
	top: 4.5em;
	right: 1em;
	transition: all .2s ease-in-out;
	z-index: 1000;
}
</style>
