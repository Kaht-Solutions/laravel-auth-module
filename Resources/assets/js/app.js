import Vue from "vue";
import VueResource from "vue-resource";
import VueRouter from "vue-router";
import VeeValidate from "vee-validate";
import _ from "lodash";
Vue.use(VueResource);
Vue.use(VueRouter);
Vue.use(VeeValidate);


window.Vue = require("vue");

Vue.http.headers.common["X-CSRF-TOKEN"] = document.head.querySelector(
    'meta[name="csrf-token"]'
).content;

Vue.http.headers.common["Accept"] = "application/json";

Vue.prototype.trans = string => _.get(window.i18n, string);

// Vue.mixin({
//     methods: {
//         trans(input) {
//             // Vue.prototype.trans = string => _.get(window.i18n, string);
//             return window.i18n;
//         }
//     }
// });
import buyer_login from "./components/buyer_login.vue";

import delivery_company_login from "./components/delivery_company_login.vue";

const router = new VueRouter({
    mode: "history",
    routes: [
        {
            path: "/auth/buyer/login",
            component: buyer_login,
        },
        {
            path: "/auth/delivery_company/login",
            component: delivery_company_login,
        }
    ]
});

const app = new Vue({
    router
}).$mount("#app");
