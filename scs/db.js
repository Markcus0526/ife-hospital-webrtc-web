var config = require('./config'),
	mysql = require('mysql'),
	conn = null;

exports.connect = function() {
	conn = mysql.createConnection({
        host: config.db_host,
        user: config.db_user,
        password: config.db_password,
        database: config.db_name
    });

    conn.connect();

    return conn;
};

exports.query = function(sql) {
    if (conn == null) {
        console.log('MySQL DB Connection is failed.');
    }

    conn.query(sql, function(err, rows, fields) {
        console.log("SQL : " + sql);
        if (err) {
            console.log(err);
        } else {
            console.log(rows);
        }
    });
};