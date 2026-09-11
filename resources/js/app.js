import Vue from 'vue'
import Vuex from 'vuex'

import App from './views/app.vue'
import store from './store'

Vue.use(Vuex)

const app = new Vue({
    el: '#app',
    store,
    components: { App }
})