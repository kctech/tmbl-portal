import Home from './vue-components/Home';
import About from './vue-components/About';

export default {
    mode: 'history',

    routes: [
        {
            path: '/',
            component: Home
        },

        {
            path: '/about',
            component: About
        }
    ]
};