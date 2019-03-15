<template>
	<div class="box">
		<p>{{comments[id].name}} - {{id}} - {{comments[id].reply_to}}</p>
		<p>{{comments[id].created_at}}</p>
		<p>{{comments[id].content}}</p>
		<comment v-for="index in comments[id].replies" :key="index" :id="index"></comment>
	</div>
</template>

<script>
export default {
	name: "comment",
	props: ['id'],
	data() {
		return {
			comments: this.$parent.comments
		}
	}
}
</script>
