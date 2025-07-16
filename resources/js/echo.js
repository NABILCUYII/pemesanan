import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

export const echo = new Echo({
    broadcaster: 'pusher',
    key: 'your-app-key',
    cluster: 'your-cluster',
    forceTLS: true,
});