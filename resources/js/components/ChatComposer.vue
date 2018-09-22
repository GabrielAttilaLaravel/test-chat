<template>
    <div class="chat-composer">
        <input type="text" placeholder="Start typing your message..." v-model="messageText"
        @keyup.enter="sendMessage">
        <button class="btn btn-primary" @click="sendMessage">Send</button>
        <form @change="fileUpload" method="POST" enctype="multipart/form-data">
            <input type="file" name="file"  >
        </form>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                messageText: '',
                imagen: null,
            }
        },
        methods: {
            sendMessage() {
                this.$emit('messagesent', {
                    message: this.messageText,
                    name: $('#navbarDropdown').text()
                });
                this.messageText = '';
            },

            fileUpload(event){
                var data = new  FormData();
                data.append('file', event.target.files[0]);
                data.append('_method', 'POST');
                axios.post('/messages/file',data)
                    .then(response => {
                        //console.log(response.data);
                        this.$emit('messagesent', {
                            message: response.data,
                            name: $('#navbarDropdown').text(),
                            imageLink: true
                        });

                    })
                this.imagen = null;
            }
        }
    }
</script>

<style scoped>
    .chat-composer {
        display: flex;
    }

    .chat-composer input{
        flex: 1 auto;
    }

    .chat-composer button {
        border-radius: 0;
    }
</style>