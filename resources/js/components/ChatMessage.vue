<template>
    <div>
        <a v-if="message.imageLink" target="_blank" :href="'http://127.0.0.1:8000/defaults/'+message.message.imageLink">
            <img  :src="'http://127.0.0.1:8000/defaults/'+message.message.imageLink">
        </a>
        <p v-else>{{ message.message }}</p>
        <small>{{ message.name }}</small>
    </div>
</template>

<script>
    export default {
        props: ['message']
    }
</script>

<style scoped>
    img {
        width: 50px;
        height: 50px;
    }

    .chat-message {
        padding: 1rem;
    }

    .chat-message > p{
        margin-bottom: .5rem;
    }

    img {
        display: block;
    }
</style>