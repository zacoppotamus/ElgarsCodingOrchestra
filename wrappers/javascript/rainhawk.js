/**
 * Project Rainhawk
 *
 * Simple PHP wrapper for the Rainhawk API, which provides simple JSON
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
		 * @return {Object} The new HTTP request object.
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
		  * @param {object} options The options to use for this request.
		  * @return {HTTPRequest} The request object.
		  */

		 send: function(options, success, failure) {
		 	if(!options.params) options.params = {};
		 	if(!options.timeout) options.timeout = 10000;

		 	if(options.method == this.methods.get && options.params.length > 0) {
		 		var params;

		 		for(var key in options.params) {
		 			params += key + "=" + options.params[key] + "&";
		 		}

		 		options.url = options.url + "?" + params.substring(0, -1);
		 	}

		 	var request = this.createRequest(options.method, options.url);

		 	request.timeout = options.timeout;
		 	request.setRequestHeader("X-Mashape-Authorization", rainhawk.apiKey);

		 	request.onload = function(e) {
		 		if(request.status == 200) {
		 			var json = JSON.parse(request.responseText);

		 			if("meta" in json && "data" in json && json.meta.code == 200) {
		 				success(json);
		 			} else {
		 				failure("data" in json && "message" in json.data ? json.data.message : "Invalid JSON received from API.");
		 			}
		 		} else {
		 			failure(request.statusText ? request.statusText : "An unknown error occured while performing the request.");
		 		}
		 	};

		 	request.onerror = function(e) {
		 		failure(request.statusText ? request.statusText : "An unknown error occured while performing the request.");
		 	};

			request.send();

			return request;
		 }
	},

	/**
	 * Send a simple ping request to the API, which will respond with
     * the timestamp of the server's current time.
     *
     * @param {function} success The function to call on success.
     * @param {function} error The function to call when an error occurs.
	 */

	ping: function(success, error) {
		var url = this.host + "/ping";

		return this.http.send({
			url: url,
			method: this.http.methods.get
		}, function(json) {
			success(json.data);
		}, error);
	}
};