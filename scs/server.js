var config = require('./config'),
	e = require('./errors'),
	fs = require('fs'),
	http = (config.ssl) ? require('https') : require('http'),
	ws = require('ws'),
	api = require('./api');

var createServer = function() {
	var httpServer = null;

	var processRequest = function(req, res) {
		res.writeHead(200);
		res.end(config.server_name + "!\n");
	};

	if (config.ssl) {
		httpServer = http.createServer({
				key: fs.readFileSync(config.ssl_key),
				cert: fs.readFileSync(config.ssl_cert)
			}, processRequest).listen(config.port);
	} else {
	    httpServer = http.createServer(processRequest).listen(config.port);
	}

	return new ws.Server({server: httpServer, path : '/'});
}

function sendTo(conn, message) {
	try {
		if (!message.err_code)
			message.err_code = e.ERR_OK;
		conn.send(JSON.stringify(message));
	}
	catch (e) {
		console.log('not send');
	}
}

function parseFrom(message) {
	var data;
	try {
		data = JSON.parse(message);
	}
	catch (e) {
		console.log('Error parsing JSON.' + message);
		data = {};
	}

	return data;
}

var	wsServer = createServer(),
	interviews = {};

function onLogin(conn, data) {
	var interview_id = data.interview_id;
	var user_id = data.user_id;
	var user_type = data.user_type;
	var token = data.token;
	var browser = data.browser;

	if (!interview_id || !user_id || !user_type) {
		sendTo(conn, { type: "login", err_code: e.ERR_INVALID_PARAMETER });
	}

	console.log(token);

	api.call("interview/enter", {
			interview_id: interview_id,
			user_id: user_id,
			user_type: user_type,
			token: token
		}, 
		function(ret) {
			// success
			console.log('User logged in as ', interview_id, ":", user_id, ":", user_type, ":", browser);
			if (!interviews[interview_id]) {
				interviews[interview_id] = {};
			}

			if (interviews[interview_id][user_type]) {
				var o_conn = interviews[interview_id][user_type];
				if (o_conn.token != token) {
					sendTo(conn, { type: "login", err_code: e.ERR_ALREADY_LOGIN });
					return;
				}
				else {
					sendTo(o_conn, { 
						type: "leave", 
						interview_id: interview_id,
						user_id: user_id,
						user_type: user_type
					});
					o_conn.close();
					delete o_conn;
				}
			}

			interviews[interview_id][user_type] = conn;
			conn.interview_id = interview_id;
			conn.user_id = user_id;
			conn.user_type = user_type;
			conn.browser = browser;
			conn.token = token;

			onlines = [];
			for (var o_user_type in interviews[interview_id])
			{
				var o_conn = interviews[interview_id][o_user_type];
				onlines.push({
					user_id: o_conn.user_id,
					user_type: o_conn.user_type
				});
			}

			for (var o_user_type in interviews[interview_id])
			{
				var o_conn = interviews[interview_id][o_user_type];

				sendTo(o_conn, { 
					type: "login", 
					interview_id: interview_id,
					onlines: onlines,
					user_type: user_type,
					doctor_offline_yet: ret.doctor_offline_yet,
					browser: browser
				});	
			}
		}, 
		function(ret) {
			// fail
			console.log("Failed enter to room:" + ret.err_code + ":" + ret.err_msg);
			sendTo(conn, { type: "login", err_code: ret.err_code });

		});
}

function onOffer(conn, data) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	var o_user_type = data.user_type;

	if (!interview_id || !user_id || !user_type) {
		sendTo(conn, { type: "offer", err_code: e.ERR_INVALID_PARAMETER });
	}

	if (!interviews[interview_id] || 
		!interviews[interview_id][o_user_type]) {
		sendTo(conn, { type: "offer", err_code: e.ERR_NO_INTERVIEW });
	}

	console.log("Sending offer from", interview_id, ":", user_type, "to", interview_id, ":", o_user_type);
	var o_conn = interviews[interview_id][o_user_type];
	sendTo(o_conn, {
		type: "offer",
		offer: data.offer,
		interview_id: interview_id,
		user_id: user_id,
		user_type: user_type
	});
}

function onAnswer(conn, data) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	var o_user_type = data.user_type;

	if (!interview_id || !user_id || !user_type) {
		sendTo(conn, { type: "answer", err_code: e.ERR_INVALID_PARAMETER });
	}

	if (!interviews[interview_id] || 
		!interviews[interview_id][o_user_type]) {
		sendTo(conn, { type: "answer", err_code: e.ERR_NO_INTERVIEW });
	}

	console.log("Sending answer from", interview_id, ":", user_type, "to", interview_id, ":", o_user_type);
	var o_conn = interviews[interview_id][o_user_type];
	sendTo(o_conn, {
		type: "answer",
		answer: data.answer,
		interview_id: interview_id,
		user_id: user_id,
		user_type: user_type
	});
}

function onCandidate(conn, data) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	var o_user_type = data.user_type;

	if (!interview_id || !user_id || !user_type) {
		sendTo(conn, { type: "candidate", err_code: e.ERR_INVALID_PARAMETER });
	}
	
	if (!interviews[interview_id] || 
		!interviews[interview_id][o_user_type]) {
		sendTo(conn, { type: "candidate", err_code: e.ERR_NO_INTERVIEW });
	}

	console.log("Sending candidate from", interview_id, ":", user_type, "to", interview_id, ":", o_user_type);
	var o_conn = interviews[interview_id][o_user_type];
	sendTo(o_conn, {
		type: "candidate",
		mlineindex: data.mlineindex,
		candidate: data.candidate,
		interview_id: interview_id,
		user_id: user_id,
		user_type: user_type
	});
}

function onMessage(conn, data) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	if (!interview_id || !user_id || !user_type ||
		data.content == '' || data.no == '') {
		sendTo(conn, { type: "message", err_code: e.ERR_INVALID_PARAMETER });
	}
	
	if (!interviews[interview_id]) {
		sendTo(conn, { type: "message", err_code: e.ERR_NO_INTERVIEW });
	}

	console.log('Send message from user ', interview_id, ":", user_type);

	for (var o_user_type in interviews[interview_id])
	{
		var o_conn = interviews[interview_id][o_user_type];

		sendTo(o_conn, { 
			type: "message", 
			interview_id: interview_id,
			user_type: user_type,
			content: data.content,
			no: data.no
		});	
	}
}

function onLive(conn) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	if (interviews[interview_id]) {
		if (interviews[interview_id][user_type]) {
			var now = new Date();
			sendTo(conn, { 
				type: "live", 
				system_now: Date.parse(now.toUTCString())
			});
		}
	}
}

function onClose(conn) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	var token = conn.token;
	if (interviews[interview_id]) {
		if (interviews[interview_id][user_type]) {
			delete interviews[interview_id][user_type];
		}

		console.log('Disconnecting user ', user_type, ' in ', interview_id);

		if (Object.keys(interviews[interview_id]).length == 0) {
			delete interviews[interview_id];
		}
		else {
			for (var o_user_type in interviews[interview_id])
			{
				if (user_type != o_user_type) {
					var o_conn = interviews[interview_id][o_user_type];

					sendTo(o_conn, { 
						type: "leave", 
						interview_id: interview_id,
						user_id: user_id,
						user_type: user_type
					});
				}
			}
		}

		api.call("interview/leave", {
			interview_id: interview_id,
			user_id: user_id,
			token: token
		}, 
		function(ret) {
			// success
		});
	}
}

function onFinish(conn, data) {
	var interview_id = conn.interview_id;
	var user_id = conn.user_id;
	var user_type = conn.user_type;
	var token = conn.token;
	if (!interview_id || !user_id || user_type != "d") {
		sendTo(conn, { type: "finish", err_code: e.ERR_INVALID_PARAMETER });
	}

	if (!interviews[interview_id]) {
		sendTo(conn, { type: "finish", err_code: e.ERR_NO_INTERVIEW });
	}

	var users = [];
	for (var o_user_type in interviews[interview_id])
	{
		users.push(o_user_type);
	}
	
	api.call("interview/finish", {
			interview_id: interview_id,
			token: token,
			users: users
		}, 
		function() {
			// success
			console.log('Finish from user ', interview_id, ":", user_type, ' in ', interview_id);
		}, 
		function(ret) {
			// fail
			console.log("Failed finishing:" + ret.err_code + ":" + ret.err_msg);
			//sendTo(conn, { type: "finish", err_code: ret.err_code });
		});

	for (var o_user_type in interviews[interview_id])
	{
		var o_conn = interviews[interview_id][o_user_type];

		sendTo(o_conn, { 
			type: "finish", 
			interview_id: interview_id,
			user_type: user_type
		});	
	}
	
}

wsServer.on('connection', function(conn) {
	console.log('A user connected.');

	conn.on('message', function(message) {
		var data = parseFrom(message);

		switch (data.type) {
			case "login":
				onLogin(conn, data);
				break;

			case "offer":
				onOffer(conn, data);
				break;

			case "answer":
				onAnswer(conn, data);
				break;

			case "candidate":
				onCandidate(conn, data);
				break;

			case "message":
				onMessage(conn, data);
				break;

			case "live":
				onLive(conn, data);
				break;

			case "leave":
				onClose(conn);
				break;

			case "finish":
				onFinish(conn, data);
				break;

			default:
				sendTo(conn, {
					type: "error",
					err_code: e.ERR_UNKNOWN_COMMAND
				});

				break;
		}
	})

	conn.on('close', function() {
		onClose(conn);
	})
});

wsServer.on('listening', function() {
	console.log(config.server_name + ' started...');
});