import axios from 'axios';

export default {
    generateApi: function (store) {
        return {
            APIUpload: function (file, responseCall, addition = null) {
                axios.interceptors.response.use(response => {
                    return response;
                }, error => {
                    return error;
                });

                let formData = new FormData();
                formData.append('file', file);
                if (addition != null) {
                    for (let key in addition) {
                        formData.append(key, addition[key]);
                    }
                }

                axios({
                    method: 'post',
                    url: store.state.settings.API_BASE + 'upload',
                    data: formData,
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    },
                    withCredentials: true
                })
                    .then(function (response) {
                        if (response.status === 200) {
                            responseCall(response.data);
                        }
                    });
            },
            APICall: function (method, service, data, responseCall, showLoader = true, files = null) {
                if (files == null) {
                    let properties = {
                        method: method,
                        url: store.state.settings.API_BASE + service,
                        json: true,
                        withCredentials: true,
                        headers: {}
                    };
                    if ((method === 'put') || (method === 'post') || (method === 'patch')) {
                        properties.data = data;
                    } else {
                        properties.params = data;
                    }
                    if (showLoader) {
                        store.commit('increaseLoaderCount');
                    }

                    axios(properties).then(function (response) {
                        if (showLoader) {
                            store.commit('decreaseLoaderCount');
                        }
                        responseCall(response.data, true);
                    }, function (error) {
                        if (showLoader) {
                            store.commit('decreaseLoaderCount');
                        }
                        responseCall(error.response.data, false);
                    });
                } else {
                    let formData = new FormData();

                    for (let key in files) {
                        formData.append(key, files[key]);
                    }

                    for (let key in data) {
                        if (typeof (data[key]) != 'object') {
                            formData.append(key, data[key]);
                        } else {
                            formData.append(key, JSON.stringify(data[key]));
                        }
                    }

                    if (showLoader) {
                        store.commit('increaseLoaderCount');
                    }

                    axios({
                        method: method,
                        url: this.$getSettings('API_BASE') + service,
                        data: formData,
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        },
                        withCredentials: true
                    }).then(function (response) {
                        if (showLoader) {
                            store.commit('decreaseLoaderCount');
                        }
                        responseCall(response.data, true);
                    }, function (error) {
                        if (showLoader) {
                            store.commit('decreaseLoaderCount');
                        }
                        responseCall(error.response.data, false);
                    });
                }
            }
        }
    }
};
