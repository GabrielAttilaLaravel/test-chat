
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

Vue.component('chat-message', require('./components/ChatMessage'));
Vue.component('chat-log', require('./components/ChatLog'));
Vue.component('chat-composer', require('./components/ChatComposer'));

const app = new Vue({
    el: '#app',
    data: {
        messages: [],
        usersInRoom: []
    },
    methods: {
        addMessage(message) {
            //console.log(message);
            //  Add to existing messages
            this.messages.push(message);
            // Persist to the database etc
            axios.post('/messages', message);
        }
    },
    created() {
        axios.get('/messages').then(response => {
            //console.log(response.data);
            this.messages = response.data;
        });

        Echo.join('chatroom')
            .here((users) => {
                this.usersInRoom = users;
            })
            .joining((user) => {
                this.usersInRoom.push(user);
            })
            .leaving((user) => {
                this.usersInRoom = this.usersInRoom.filter(u => u != user);
            })
            .listen('MessagePosted', (e) => {
                //console.log(e);
                this.messages.push({
                    message: e.message.message,
                    name: e.message.name
                })
            });
    }
});
