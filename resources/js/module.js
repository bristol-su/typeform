import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';
import http from 'http-client';
import AWN from "awesome-notifications";

import Responses from './components/responses/Responses';
import TypeformEmbedWidget from './components/embed/TypeformEmbedWidget';
import TypeformEmbedPopup from './components/embed/TypeformEmbedPopup';
Vue.prototype.$http = http;
Vue.prototype.$notify = new AWN({position: 'top-right'});
Vue.use(BootstrapVue);
Vue.prototype.$url = portal.APP_URL + '/' + portal.A_OR_P + '/' + portal.ACTIVITY_SLUG + '/' + portal.MODULE_INSTANCE_SLUG + '/' + portal.ALIAS;

let vue = new Vue({
    el: '#typeform-root',
    
    components: {
        TypeformEmbedWidget,
        TypeformEmbedPopup,
        Responses
    }
});