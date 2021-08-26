import Vue from 'vue';
import AWN from "awesome-notifications";

import Responses from './components/responses/Responses';
import TypeformEmbedWidget from './components/embed/TypeformEmbedWidget';
import TypeformEmbedPopup from './components/embed/TypeformEmbedPopup';
import Toolkit from '@bristol-su/frontend-toolkit';
Vue.use(Toolkit);

Vue.prototype.$notify = new AWN({position: 'top-right'});

let vue = new Vue({
    el: '#typeform-root',

    components: {
        TypeformEmbedWidget,
        TypeformEmbedPopup,
        Responses
    }
});
