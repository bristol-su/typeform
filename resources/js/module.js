import Vue from 'vue';

import Responses from './components/responses/Responses';
import TypeformEmbedWidget from './components/embed/TypeformEmbedWidget';
import Toolkit from '@bristol-su/frontend-toolkit';
Vue.use(Toolkit);


let vue = new Vue({
    el: '#typeform-root',

    components: {
        TypeformEmbedWidget,
        Responses
    }
});
