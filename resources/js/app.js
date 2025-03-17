import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();




import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: true
});

window.Echo.channel('chat.' + userId)
    .listen('ChatMessage', (e) => {
        console.log(e);
        document.querySelector('.chat-box').innerHTML += `<div class="received"><p>${e.message.message}</p></div>`;
    });

