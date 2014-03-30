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
		 * @return {Object}
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
		  * @return {HTTPRequest}
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

		 	if(options.method == this.methods.post) {
		 		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		 		request.setRequestHeader("Content-Length", params.length);
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
		 * @param {function} success
	     * @param {function} error
		 */

		list: function(success, error) {
		 	var url = rainhawk.host + "/datasets";

		 	return rainhawk.http.send({
				url: url,
				method: rainhawk.http.methods.get
			}, function(json) {
				success(json.data.datasets);
			}, error);
		},

		/**
		 * Fetch some information about a specific dataset, returning an array
		 * of data that can be used to gather more insight into what the dataset
		 * currently looks like.
		 *
		 * @param {string} name
		 * @param {function} success
		 * @param {function} error
		 */

		info: function(name, success, error) {
			var url = rainhawk.host + "/datasets/" + name;

		 	return rainhawk.http.send({
				url: url,
				method: rainhawk.http.methods.get
			}, function(json) {
				success(json.data);
			}, error);
		},

		/**
		 * Create a new dataset using the POST method on the same endpoint as
		 * above, sending only the name of the dataset and not the username as
		 * well.
		 *
		 * @param {string} name
		 * @param {string} description
		 * @param {function} success
		 * @param {function} error
		 */

		create: function(name, description, success, error) {
			var url = rainhawk.host + "/datasets";
			var params = {name: name, description: description};

		 	return rainhawk.http.send({
				url: url,
				method: rainhawk.http.methods.post,
				params: params
			}, function(json) {
				success(json.data);
			}, error);
		},

		/**
		 * Remove a dataset using the DELETE method on the dataset's endpoint
		 *
		 * @param {string} name
		 * @param {function} success
		 * @param {function} error
		 */

		delete: function(name, success, error) {
			var url = rainhawk.host + "/datasets/" + name;

		 	return rainhawk.http.send({
				url: url,
				method: rainhawk.http.methods.del
			}, function(json) {
				success(json.data.deleted);
			}, error);
		}
	},

	/**
	 * Operations specific to data points go here.
	 */

	data: {
		/**
		 * Fetch some data from the dataset, optionally specifying a query to
		 * filter the rows that will be returned.
		 *
		 * @param {string} name
		 * @param {object} options
		 * @param {function} success
		 * @param {function} error
		 */

		select: function(name, options, success, error) {
			if(!options) options = {};

			var url = rainhawk.host + "/datasets/" + name + "/data";
			var params = {
				query: options.hasOwnProperty("query") ? JSON.stringify(options.query) : null,
				offset: options.hasOwnProperty("offset") ? options.offset : 0,
				limit: options.hasOwnProperty("limit") ? options.limit : 0,
				sort: options.hasOwnProperty("sort") ? JSON.stringify(options.sort) : null,
				field: options.hasOwnProperty("field") ? JSON.stringify(options.field) : null,
				exclude: options.hasOwnProperty("exclude") ? JSON.stringify(options.exclude) : null
			};

		 	return rainhawk.http.send({
				url: url,
				method: rainhawk.http.methods.get,
				params: params
			}, function(json) {
				success(json.data);
			}, error);
		},

		/**
		 * Insert a single row of data into the specified dataset, which is
		 * just an alias for insertMulti providing an array of one row instead.
		 *
		 * @param {string} name
		 * @param {object} row
		 * @param {function} success
		 * @param {function} error
		 */

		insert: function(name, row, success, error) {
			return this.insertMulti(name, [row], function(rows) {
				success(rows[0]);
			}, error);
		},

		/**
		 * Insert multiple rows of data into the specified dataset, which calls
		 * the API with an array of objects which are sent in batch.
		 *
		 * @param {string} name
		 * @param {array} rows
		 * @param {function} success
		 * @param {function} error
		 */

		insertMulti: function(name, rows, success, error) {
		},

		/**
		 *
		 */

		update: function(name, query, changes, success, error) {
		},

		/**
		 *
		 */

		delete: function(name, query, success, error) {
		}
	},

	/**
	 * Support function to allow the library to count the size of an object,
	 * which JS doesn't have native support for.
	 *
	 * @param {object} obj
	 * @return {int}
	 */

	objectSize: function(obj) {
		var size = 0, key;

	    for(key in obj) {
	        if(obj.hasOwnProperty(key)) size++;
	    }

	    return size;
	}
};