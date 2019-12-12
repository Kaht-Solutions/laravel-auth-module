<template>
  <div class="d-flex justify-content-center">
    <div class="col-md-4">
      <h2 class="text-center"></h2>

      <div v-if="alerts.length" class="alert">
        <div
          v-for="alert in alerts"
          :class="alert.class"
          v-bind:key="alert.message"
        >{{ alert.message }}</div>
      </div>

      <form @submit.prevent="login">
        <div class="form-group">
          <input
            class="form-control"
            placeholder="mobile"
            type="text"
            v-model="mobile"
            v-validate.initial="{required:true}"
            name="mobile"
            autofocus
          />
        </div>
        <div class="form-group">
          <input
            class="form-control"
            placeholder="password"
            type="password"
            v-model="password"
            v-validate.initial="{required:true}"
            name="password"
            autofocus
          />
        </div>

        <div class="form-group">
          <button class="btn btn-info btn-circle">{{ trans('auth_messages.login') }}</button>
        </div>
      </form>
    </div>
  </div>
</template>
    <script>
export default {
  data() {
    return {
      mobile: "09367034765",
      password: null,
      loading: null,
      alerts: []
    };
  },
  created() {},
  methods: {
    login() {
      this.alerts = [];

      this.loading = true;

      if (this.errors.items.length > 0) {
        this.errors.items.forEach(element => {
          this.alerts.push({
            message: element.msg,
            class: "error"
          });
        });
        this.loading = false;
        return false;
      }

      this.$http
        .post("/api/auth/delivery_company/login", {
          
          mobile: this.mobile,
          password: this.password,
          is_cookie:true
        })
        .then(response => {
          console.log(response.body);
          if (response.body.is_successful) {
            this.loading = false;
            location.href =
              "http://" + window.location.hostname + "/delivery_company/mypanel";
          } else {
            this.alerts = [
              {
                message: response.body.message,
                class: "error"
              }
            ];
          }
          this.loading = false;
        });
    }
  }
};
</script>