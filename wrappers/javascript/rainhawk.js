/**
 * Project Rainhawk
 *
 * Simple Javascript wrapper for the Rainhawk API, which provides simple JSON
 * encoded data for a variety of data sources with pre-defined
 * operations.
 *
 * @package Rainhawk
 * @license none
 */

var rainhawk = {
    /**
     * Set the base URL for the API.
     *
     * @var {string}
     */

    host: "https://sneeza-eco.p.mashape.com",

    /**
     * Store the user's Mashape API key.
     *
     * @var {string}
     */

    apiKey: null,

    /**
     * Create an object that can be used for communicating at an HTTP level
     * with the API.
     */

    http: {
        /**
         * Store the supported methods for communicating with the API, which
         * are only the standard HTTP protocol methods.
         */

        methods: {
            get: "GET",
            post: "POST",
            put: "PUT",
            del: "DELETE"
        },

        /**
         * Method to create a new instance of a HTTP request using the above
         * factories. Each one is tested in turn until a valid object is received
         * at which point we can send the result back.
         *
         * @return {object}
         */

         createRequest: function(method, url) {
            var request = new XMLHttpRequest();

            if("withCredentials" in request) {
                request.open(method, url, true);

                return request;
            } else if(typeof XDomainRequest != "undefined") {
                request = new XDomainRequest();
                request.open(method, url);

                return request;
            }

            return;
         },

         /**
          * Send a HTTP request to the specified endpoint so that we can get
          * some data back and process requests.
          *
          * @param {object} options
          * @return {httprequest}
          */

         send: function(options, success, failure) {
            if(!options.params) options.params = {};
            if(!options.timeout) options.timeout = 10000;

            var request;
            var params;

            if(rainhawk.objectSize(options.params) > 0) {
                params = "";

                for(var key in options.params) {
                    params += key + "=" + encodeURIComponent(options.params[key]) + "&";
                }

                params = params.substring(0, params.length - 1);

                if(options.method == this.methods.get) {
                    options.url = options.url + "?" + params;
                    params = null;
                }
            }

            request = this.createRequest(options.method, options.url);
            request.timeout = options.timeout;
            request.setRequestHeader("X-Mashape-Authorization", rainhawk.apiKey);

            if(params && !options.hasOwnProperty("file")) {
                request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            } else if(options.method == this.methods.put && options.hasOwnProperty("file")) {
                params = options.file;
            }

            request.onload = function(e) {
                if(request.status == 200) {
                    var json = JSON.parse(request.responseText);

                    if(json.hasOwnProperty("meta") && json.hasOwnProperty("data") && json.meta.code == 200) {
                        success(json);
                    } else {
                        failure(json.hasOwnProperty("data") && json.data.hasOwnProperty("message") ? json.data.message : "Invalid JSON received from API.");
                    }
                } else {
                    failure(request.statusText ? request.statusText : "An unknown error occured while performing the request.");
                }
            };

            request.onerror = function(e) {
                failure(request.statusText ? request.statusText : "An unknown error occured while performing the request.");
            };

            request.send(params);

            return request;
         }
    },

    /**
     * Send a simple ping request to the API, which will respond with
     * the timestamp of the server's current time.
     *
     * @param {function} success
     * @param {function} error
     */

    ping: function(success, error) {
        var url = rainhawk.host + "/ping";

        return rainhawk.http.send({
            url: url,
            method: rainhawk.http.methods.get
        }, function(json) {
            success(json.data);
        }, error);
    },

    /**
     * Operations specific to datasets go in here.
     */

    datasets: {
        /**
         * List the datasets that the current user can access (either read or
         * write) and some basic information about them.
         *
         
