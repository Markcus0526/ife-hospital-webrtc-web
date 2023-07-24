var config = require('./config'),
	e = require('./errors'),
	request = require('request');

exports.call = function(method, params, cbSuccess, cbFail) {
	var url = config.api_prefix + method;

	var options = {
		uri: url,
		method: 'POST',
		json: params
	};
	process.env.NODE_TLS_REJECT_UNAUTHORIZED = "0";
	request(options, function (error, response, ret) {
		if (!error && response.statusCode == 200) {
			if (ret.err_code == e.ERR_OK) {
				if (cbSuccess)
					cbSuccess(ret);
			}
			else {
				if (cbFail)
					cbFail(ret);
			}
		}
		else {
			if (cbFail)
				cbFail({ err_code: e.ERR_CONNECT_FAIL_API});
		}
	});
}