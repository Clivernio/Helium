var helium_app = helium_app || {};

// Login Page
helium_app.login_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_login',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            loginAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }

                        setTimeout(() => {
                            location.href = _form.attr('data-redirect-url');
                        }, 3000);
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Reset Password Page
helium_app.reset_password_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_reset_password',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            resetPasswordAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }

                        setTimeout(() => {
                            location.href = _form.attr('data-redirect-url');
                        }, 3000);
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Install Page
helium_app.install_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_install',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            installAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }

                        setTimeout(() => {
                            location.href = _form.attr('data-redirect-url');
                        }, 3000);
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Forgot Password Page
helium_app.forgot_password_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_forgot_password',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            forgotPasswordAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }

                        setTimeout(() => {
                            location.href = _form.attr('data-redirect-url');
                        }, 3000);
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Admin Settings Page
helium_app.settings_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_settings',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            settingsAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }
                         _form.find("button").removeAttr("disabled");
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Admin Profile Page
helium_app.profile_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_profile',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            profileAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }
                         _form.find("button").removeAttr("disabled");
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Subscriber Index Page
helium_app.subscriber_index_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_subscriber_index',
        data() {
            return {
                offset: 0,
                showLoad: false,
                subscribers: [

                ],
            }
        },
        methods: {
            getNextSubscribers(event) {
                axios.get(app_globals.subscriber_v1_list_endpoint + "?offset=" + this.offset)
                    .then((response) => {
                        this.subscribers = this.subscribers.concat(response.data.subscribers);
                        this.offset += 20;

                        if (this.offset >= response.data._metadata.totalCount) {
                            this.showLoad = false;
                        } else {
                            this.showLoad = true;
                        }
                    })
                    .catch((error) => {
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                    });
            }
        },
        mounted() {
            axios.get(app_globals.subscriber_v1_list_endpoint + "?offset=" + this.offset)
                .then((response) => {
                    this.subscribers = this.subscribers.concat(response.data.subscribers);
                    this.offset += 20;

                    if (this.offset >= response.data._metadata.totalCount) {
                        this.showLoad = false;
                    } else {
                        this.showLoad = true;
                    }
                })
                .catch((error) => {
                    toastr.clear();
                    toastr.error(error.response.data.errorMessage);
                });
        }
    });

}

// Subscriber Add Page
helium_app.subscriber_add_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_subscriber_add',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            subscriberAddAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.post(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }

                        setTimeout(() => {
                            location.href = _form.attr('data-redirect-url');
                        }, 3000);
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

// Subscriber Edit Page
helium_app.subscriber_edit_screen = (Vue, axios, Cookies, $) => {

    return new Vue({
        delimiters: ['${', '}'],
        el: '#app_subscriber_edit',
        data() {
            return {
                isInProgress: false,
            }
        },
        methods: {
            subscriberEditAction(event) {
                event.preventDefault();
                this.isInProgress = true;

                let inputs = {};
                let _self = $(event.target);
                let _form = _self.closest("form");

                _form.find("button").attr("disabled", "disabled");

                _form.serializeArray().map((item, index) => {
                    inputs[item.name] = item.value;
                });

                axios.put(_form.attr('action'), inputs)
                    .then((response) => {
                        if (response.status >= 200) {
                            toastr.clear();
                            toastr.info(response.data.successMessage);
                        }

                        setTimeout(() => {
                            location.href = _form.attr('data-redirect-url');
                        }, 3000);
                    })
                    .catch((error) => {
                        this.isInProgress = false;
                        // Show error
                        toastr.clear();
                        toastr.error(error.response.data.errorMessage);
                        _form.find("button").removeAttr("disabled");
                    });
            }
        }
    });

}

$(document).ready(() => {
    axios.defaults.headers.common = {
        'X-Requested-With': 'XMLHttpRequest'
    };

    if (document.getElementById("app_login")) {
        helium_app.login_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_reset_password")) {
        helium_app.reset_password_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_install")) {
        helium_app.install_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_forgot_password")) {
        helium_app.forgot_password_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_settings")) {
        helium_app.settings_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_profile")) {
        helium_app.profile_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_subscriber_index")) {
        helium_app.subscriber_index_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_subscriber_add")) {
        helium_app.subscriber_add_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }

    if (document.getElementById("app_subscriber_edit")) {
        helium_app.subscriber_edit_screen(
            Vue,
            axios,
            Cookies,
            $
        );
    }
});
