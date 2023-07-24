var interviewRoom = function(config) {
    var defaults = {
            scs_url: 'wss://localhost:18888',
            rcd_url: '',
            ice_servers: [{url:"stun:stun.l.google.com:19302"}],
            interview_id: null,
            user_id: null,
            user_type: null,
            token: null,
            need_interpreter: 1,
            rcd_fragment_interval: 30000,
            rcd_v_bitrate: 150000,
            messages: [],
            err: {
                ERR_CONNECT_FAIL_API: [1000, "对不起，不能连接服务器。"],
                ERR_INVALID_PARAMETER: [1001, "对不起，您没有进入该会诊室的权限。"],
                ERR_ALREADY_LOGIN: [1002, "对不起，您在别的地方中已经进入该会诊室。"],
                ERR_UNKNOWN_COMMAND: [1003, "对不起，服务器无法处理您的需求。"],
                ERR_INTERVIEW_NOT_PROGRESS: [1004, "因为该会诊还没开始，您无法进入。"],
                ERR_NO_INTERVIEW: [1005, ""],
                ERR_NOPRIV: [10, ""]
            },
            msg: {
                PATIENT: "患者",
                DOCTOR: "医生",
                INTERPRETER: "翻译",
                ONLINE: "在线",
                OFFLINE: "掉线",
                ALERT: "提示",
                PATIENT_ONLINE: "患者已在线",
                DOCTOR_ONLINE: "医生已在线",
                INTERPRETER_ONLINE: "翻译已在线",
                PATIENT_OFFLINE: "患者已掉线",
                DOCTOR_OFFLINE: "医生已掉线",
                INTERPRETER_OFFLINE: "翻译已掉线",
                ALERT_ERROR: "错误发生",
                INSTALL_CAMERA: "请安装摄像头和麦克风。",
                PERMIT_CAMERA: "请允许摄像头和麦克风的使用。",
                COULDNOT_ACCESS_CAMERA: "不能连接摄像头和麦克风。",
                CONNECTING: "服务器连接中，请耐心等候。",
                CONNECT_FAIL: "对不起，暂不能连接会诊服务器，请稍后再连接。",
                UNSUPPORT_WEBRTC: "对不起， 您使用的阅览器不支持影像对话，请使用Chrome等阅览器。",
                CONFIRM_FINISH_TITLE: "结束",
                CONFIRM_FINISH: "确定结束此次会诊？",
                FINISHING: "会诊结束中。",
                AUTOFINISHING: "医生还没上线，将会诊自动结束。",
                FINISHED: "会诊已结束。",
                WAITING: "请稍等..."
            },
            quality: 'm',
            qualities: {
                h: {
                    video : true
                },
                m: {
                    video : {
                        mandatory: {
                            maxWidth: 640,
                            maxHeight: 480
                        }
                    },
                    v_bitrate: 500, // 500kbps
                    a_bitrate: 128 // 128kbps
                },
                l: {
                    video : {
                        mandatory: {
                            maxWidth: 320,
                            maxHeight: 240
                        }
                    },
                    v_bitrate: 100, // 100kbps
                    a_bitrate: 30 // 30kbps
                }
            }
        };      
    var c = null, 
        utypes = ["p", "d", "i", "a"],
        connWs = null, 
        connRtc = {}, 
        onlines = {}, 
        videos = {}, 
        constraints = {
            mandatory: {
                OfferToReceiveAudio: true,
                OfferToReceiveVideo: true
            }
        },
        myStream = null,
        opened_camera = false,
        loginned = false,
        sys_now_offset = 0,
        doctor_loginned = false,
        timeoutAlertAutoFinish = null,
        intCheckStatus = null,
        console_log = true, 
        exited = false;

    var mediaSource, 
        mediaRecorder,
        recordedBlobs,
        sourceBuffer,
        recordTimestamp = null,
        recordNo,
        completeUpload = true,
        closing = false;

    var wh_rate = 0.75; // width:height = 4:3
    var inited_chat_input = false;

    videos.p = document.querySelector('#video_p');
    videos.d = document.querySelector('#video_d');
    videos.i = document.querySelector('#video_i');

    var print = function() {
        if (console_log) {
            try {
                console.log.apply(this, arguments);
            }
            catch(e) {
                console.log(arguments);
            }
        }
    }

    var go = function(url, forceClosing) {
        if (forceClosing === undefined || forceClosing == true) {
            showMask(c.msg.WAITING);
            if (connWs) {
                closing = true;
                stopRecording();
                connWs.close();
            }
        }

        if (completeUpload) {
            goto_url(url);
        }
        else {
            // wait for uploading 
            setTimeout("interviewRoom.go('" + url + "', false)", 1000);
        } 
    }

    var refreshEnables = function() {
        // if (opened_camera && loginned) {
        //     $('.btn-start').removeAttr('disabled');
        // }
        // else {
        //     $('.btn-start').attr('disabled', 'disabled');   
        // }

        if (loginned) {
            if (c.user_type != 'a') {
                $('#chat_input').removeAttr('disabled');
                $('#btn_send_message').removeAttr('disabled');
            }
            hideMask();
        }
        else {
            $('#chat_input').attr('disabled', 'disabled');
            $('#btn_send_message').attr('disabled', 'disabled');
            setTimeout(function() {
                if (!loginned && !exited)
                    showMask(c.msg.CONNECT_FAIL);
            }, 1000);
        }
    }

    var sendTo = function(message) {
        connWs.send(JSON.stringify(message));
    }

    var parseFrom = function(message) {
        var data;
        try {
            data = JSON.parse(message);
        }
        catch (e) {
            print('Error parsing JSON.' + message);
            data = {};
        }

        return data;
    }

    var hasUserMedia = function() {
        return !!navigator.getUserMedia;
    }

    var hasRTCPeerConnection = function() {
        return !!window.RTCPeerConnection;
    }

    var openMyMedia = function() {
        if (c.user_type == "a") {
            opened_camera = true;
            setupPeerConnections(null);
        }
        else {
            if (hasUserMedia()) {
                mediaSource = new MediaSource();
                mediaSource.addEventListener('sourceopen', function() {
                    print('MediaSource opened');
                    sourceBuffer = mediaSource.addSourceBuffer('video/webm; codecs="vp8"');
                    print('Source buffer: ', sourceBuffer);
                }, false);

                var q = c.qualities[c.quality];
                navigator.getUserMedia({ 
                    video: ((q == undefined) ? true : q.video), 
                    audio: true
                }, function(stream) {
                    myStream = stream;
                    videos[c.user_type].srcObject = myStream;
                    videos[c.user_type].muted = true; // myself video mute
                    //initRecording(myStream);

                    if (hasRTCPeerConnection()) {
                        $('#media_error_' + c.user_type).text('');
                        opened_camera = true;
                        setupPeerConnections(stream);
                    } else {
                        $('#media_error_' + c.user_type).text(c.msg.UNSUPPORT_WEBRTC);
                    }
                }, function(e) {
                    switch(e.name) {
                        case "DevicesNotFoundError":
                        case "NotFoundError":
                            $('#media_error_' + c.user_type).text(c.msg.INSTALL_CAMERA);
                            break;
                        case "SecurityError":
                        case "PermissionDeniedError":
                            $('#media_error_' + c.user_type).text(c.msg.PERMIT_CAMERA);
                            break;
                        default:
                            $('#media_error_' + c.user_type).text(c.msg.COULDNOT_ACCESS_CAMERA);
                            print("Error opening your camera and/or microphone: " + e.message);
                            break;
                    }

                    setTimeout(interviewRoom.openMyMedia, 3000);
                });
            }
            else {
                $('#media_error_' + c.user_type).text(c.msg.UNSUPPORT_WEBRTC);
            }
        }
    }

    var onConnAddstream = function(ut, e) {
        videos[ut].srcObject = e.stream;
    }
    var onConnAddstreams = {
        p: function(e) { onConnAddstream("p", e); },
        d: function(e) { onConnAddstream("d", e); },
        i: function(e) { onConnAddstream("i", e); },
        a: function(e) {  }
    }

    var onConnIceCandidate = function(ut, e) {
        if (e.candidate) {
            print("candidate ", e.candidate);
            sendTo({ 
                type: "candidate",
                mlineindex: e.candidate.sdpMLineIndex,
                candidate: e.candidate.candidate,
                user_type: ut
            });
        }
    }
    var onConnIceCandidates = {
        p: function(e) { onConnIceCandidate("p", e); },
        d: function(e) { onConnIceCandidate("d", e); },
        i: function(e) { onConnIceCandidate("i", e); },
        a: function(e) { onConnIceCandidate("a", e); }
    }

    var onConnClose = function(ut, e) {
        print("close peer connection ", ut);
        connRtc[ut].sentOffer = false;

    }
    var onConnCloses = {
        p: function(e) { onConnClose("p", e); },
        d: function(e) { onConnClose("d", e); },
        i: function(e) { onConnClose("i", e); },
        a: function(e) { onConnClose("a", e); }
    }
    var connConfig = null;

    var setupPeerConnection = function(stream, ut) {
        conn = new RTCPeerConnection(connConfig);

        conn.sentOffer = false;
        conn.user_type = ut;
        if (stream)
            conn.addStream(stream);
        conn.onaddstream = onConnAddstreams[ut];
        conn.onicecandidate = onConnIceCandidates[ut];
        conn.onclose = onConnClose[ut];

        connRtc[ut] = conn;
    }

    var setupPeerConnections = function(stream, user_type) {
        for (var i in utypes) {
            ut = utypes[i];
            if (ut != c.user_type && (user_type == undefined || ut == user_type)) {
                setupPeerConnection(stream, ut);
            }
        }

        refreshEnables();
        start(user_type);
    }

    var start = function(user_type) {
        if (!(opened_camera && loginned)) {
            return;
        }
        for (var i in onlines) {
            var ut = onlines[i].user_type;
            if (c.user_type != ut)
            {
                if (user_type == undefined || user_type == ut) {
                    startConnection(ut);
                }
            }
        }
    }

    /*********************************
    *       bitrate control          *   
    *********************************/
    var setMediaBitrates = function(sdp) {
        var q = c.qualities[c.quality];
        if (q == undefined) {
            v_bitrate = null; a_bitrate = null;
        }
        else {
            v_bitrate = q.v_bitrate;
            a_bitrate = q.a_bitrate;
        }
        sdp = setMediaBitrate(setMediaBitrate(sdp, "video", v_bitrate), "audio", a_bitrate);
        return sdp;
    }
    var setMediaBitrate = function(sdp, media, bitrate) {
        if (bitrate == null || bitrate == undefined)
            return sdp;
        var lines = sdp.split("\n");
        var line = -1;
        for (var i = 0; i < lines.length; i++) {
        if (lines[i].indexOf("m="+media) === 0) {
            line = i;
            break;
        }
        }
        if (line === -1) {
            // Could not find the m line
            return sdp;
        }
        // Pass the m line
        line++;
        // Skip i and c lines
        while(lines[line].indexOf("i=") === 0 || lines[line].indexOf("c=") === 0) {
            line++;
        }
        bline = "b=AS:"+bitrate+"\r";
        // If we're on a b line, replace it
        if (lines[line].indexOf("b") === 0) {
            print("Replaced b line with ", bitrate);
            lines[line] = bline;
            return lines.join("\n");
        }
        
        // Add a new b line
        print("Adding new b line with ", bitrate);
        var newLines = lines.slice(0, line);
        newLines.push(bline);
        newLines = newLines.concat(lines.slice(line, lines.length));
        return newLines.join("\n");
    }

    /*********************************
    *           signaling            *   
    *********************************/
    var startConnection = function(ut) {
        connRtc[ut]
            .createOffer(constraints)
            .then(function(offer) {
                offer.sdp = setMediaBitrates(offer.sdp);
                connRtc[ut].setLocalDescription(offer);
                sendTo({
                    type: "offer",
                    offer: offer,
                    user_type: ut
                });

                connRtc[ut].sentOffer = true;
            })
            .catch(function (error) {
                print(error);
            }); 
    }

    var reoffer = function(ut) {
        print("start reoffer");
        if (ut == undefined) {
            for (var i in onlines) {
                var _ut = onlines[i].user_type;
                if (_ut != c.user_type) {
                    reoffer(_ut);
                }
            }
        }
        else
            setupPeerConnections(myStream, ut);
    }

    var login = function() {
        sendTo({
            type: "login",
            interview_id: c.interview_id,
            user_id: c.user_id,
            user_type: c.user_type,
            token: c.token,
            browser: $.browser
        });
    }

    var finish = function() {
        showMask(c.msg.FINISHING);
        sendTo({
            type: "finish",
            interview_id: c.interview_id
        });
    }

    var live = function() {
        if (loginned) {
            sendTo({
                type: "live",
            });     
        }
    }

    var refreshWaitingDoctor = function() {
        if (!doctor_loginned) {
            $('.alert-waiting-doctor').removeClass('hide');
        }
        else {
            $('.alert-waiting-doctor').addClass('hide');    
        }

        resizeLayouts();
    }

    var checkStatus = function() {
        App.callAPI("api/interview/status",
            {
                interview_id: c.interview_id
            }
        )
        .done(function(res) {
            if (res.status == 6) {
                // 进行中
            }
            else if (res.status == 8) {
                // 已完成
                showMask(c.msg.FINISHING);
                setTimeout(function() {
                    connWs.close();
                    interviewRoom.go('interview');
                }, 2000);
            }
            else if (res.status == 10) {
                // 已失效
                showMask(c.msg.FINISHING);
                setTimeout(function() {
                    connWs.close();
                    interviewRoom.go('interview');
                }, 2000);
            }
            else {
                connWs.close();
                interviewRoom.go('interview');
            }
        })
        .fail(function(res) {
            
        });
    }

    var isOnline = function(user_type) {
        for (var i in onlines) {
            var ut = onlines[i].user_type;
            if (ut == user_type)
            {
                return true;
            }
        }
        return false;
    }

    var refreshOnlines = function() {
        var offlines = ["p", "d", "i"];
        var total = 2, online_count = 0;
        if (c.need_interpreter == '1') {
            total ++;
        }
        for (var i in onlines) {
            var user_type = onlines[i].user_type;
            $('#state_' + user_type).text(c.msg.ONLINE);
            switch(user_type) {
                case "p":
                    m = c.msg.PATIENT_ONLINE;
                    online_count ++;
                    break;
                case "d":
                    m = c.msg.DOCTOR_ONLINE;
                    online_count ++;
                    break;
                case "i":
                    m = c.msg.INTERPRETER_ONLINE;
                    online_count ++;
                    break;
                default:
                    m = ""; // admin
                    break;
            }

            for (var j in offlines) {
                if (user_type == offlines[j])
                {
                    delete offlines[j];
                }
            }

            $('#state_msg_' + user_type).text(m);
        }

        for (var j in offlines) {
            var user_type = offlines[j];
            $('#state_' + user_type).text(c.msg.OFFLINE);
            switch(user_type) {
                case "p":
                    m = c.msg.PATIENT_OFFLINE;
                    break;
                case "d":
                    m = c.msg.DOCTOR_OFFLINE;
                    break;
                case "i":
                    m = c.msg.INTERPRETER_OFFLINE;
                    break;
                default:
                    m = ""; // admin
                    break;
            }

            $('#state_msg_' + user_type).text(m);
        }

        $('#stats').text(" (" + online_count + "/" + total + ")");
    }

    var autoFinish = function() {
        if (!isOnline('d')) {
            showMask(c.msg.AUTOFINISHING);
            sendTo({
                type: "finish",
                interview_id: c.interview_id
            });
        }
    }

    var getErrMsg = function(err_code) {
        for (var e in c.err) {
            e = c.err[e];
            if (e[0] == err_code)
                return e[1];
        }
    }

    var onLogin = function(data) {
        if (data.err_code == 0) {
            clearState();
            if (data.user_type == 'd') {
                doctor_loginned = true;
                refreshWaitingDoctor();
            }

            onlines = data.onlines;
            refreshOnlines();

            if (data.user_type == c.user_type) {
                loginned = true;
                refreshEnables();
                start();
                interviewRoom.live();
                
                if (data.doctor_offline_yet !== false)
                {
                    print("doctor is offline yet.");
                    timeoutAlertAutoFinish = setTimeout(interviewRoom.refreshWaitingDoctor, data.doctor_offline_yet*1000);
                }
            }
        }
        else if (data.err_code == c.err.ERR_NO_INTERVIEW[0] || 
            data.err_code == c.err.ERR_NOPRIV[0]) {
            interviewRoom.go('interview');
        }
        else {
            errorBox(c.msg.ALERT_ERROR, getErrMsg(data.err_code), function() {
                interviewRoom.go('interview');
            });
        }
    }

    var onOffer = function(data) {
        var ut = data.user_type;
        if (data.err_code == 0) {
            if (connRtc[ut].sentOffer)
            {
                setupPeerConnection(myStream, ut);
            }
            offer = data.offer;
            //offer.sdp = fixOlderChromeError(offer.sdp);
            connRtc[ut]
                .setRemoteDescription(new RTCSessionDescription(offer))
                .then(function() {
                    connRtc[ut]
                        .createAnswer(constraints)
                        .then(function(answer) {
                            answer.sdp = setMediaBitrates(answer.sdp);
                            print("answer ", answer);
                            var ret = connRtc[ut].setLocalDescription(answer);
                            initRecording(myStream);
                            sendTo({
                                type: "answer",
                                answer: answer,
                                user_type: ut
                            });

                            return ret;
                        })
                        .catch(function(error) {
                            print(error);
                        });
                })
                .catch(function(error) {
                    print("onOffer ", error);

                    // reoffer
                    setTimeout("interviewRoom.reoffer('" + ut + "')", 1000);
                });
        }
        else if (data.err_code == c.err.ERR_NO_INTERVIEW[0] || 
            data.err_code == c.err.ERR_NOPRIV[0]) {
            interviewRoom.go('interview');
        }
        else {
            errorBox(c.msg.ALERT_ERROR, getErrMsg(data.err_code), function() {
                interviewRoom.go('interview');
            });
        }
    }

    var onAnswer = function(data) {
        var ut = data.user_type;
        if (data.err_code == 0) {
            connRtc[ut]
                .setRemoteDescription(new RTCSessionDescription(data.answer))
                .catch(function(error) {
                    print("onAnswer ", error);

                    // reoffer
                    setTimeout("interviewRoom.reoffer('" + ut + "')", 1000);
                });

            initRecording(myStream);

        }
        else if (data.err_code == c.err.ERR_NO_INTERVIEW[0] || 
            data.err_code == c.err.ERR_NOPRIV[0]) {
            interviewRoom.go('interview');
        }
        else {
            errorBox(c.msg.ALERT_ERROR, getErrMsg(data.err_code), function() {
                interviewRoom.go('interview');
            });
        }
    }

    var onCandidate = function(data) {
        var ut = data.user_type;
        if (data.err_code == 0) {
            connRtc[ut]
                .addIceCandidate(new RTCIceCandidate({
                    sdpMLineIndex:data.mlineindex,
                    candidate:data.candidate}))
                .catch(function(error) {
                    print("onCandidate ", error);
                });
        }
        else if (data.err_code == c.err.ERR_NO_INTERVIEW[0] || 
            data.err_code == c.err.ERR_NOPRIV[0]) {
            interviewRoom.go('interview');
        }
    }

    var onMessage = function(data) {
        if (data.err_code == 0) {
            msg = {
                user_type: data.user_type,
                user_name: getUserName(data.user_type),
                content: data.content,
                no: data.no
            }

            insertMessage(msg);
            scrollChatBottom();

            $('#msg_' + msg.no + ' .sending').addClass('hide');
        }
    }

    var onLive = function(data) {
        if (data.system_now) {
            var now = Date.parse((new Date()).toString());
            sys_now_offset = data.system_now - now;
            print("Sync time + " + sys_now_offset);
        }

        if (loginned) {
            setTimeout(interviewRoom.live, 30000);
        }
    }

    var onLeave = function(data) {
        if (data.err_code == 0) {
            var user_type = data.user_type;
            if (user_type != c.user_type && videos[user_type])
            {
                // clear video
                videos[user_type].srcObject = null;
            }

            for (var i in onlines) {
                var ut = onlines[i].user_type;
                if (user_type == ut)
                {
                    delete onlines[i];
                }
            }

            refreshOnlines(user_type);

            connRtc[user_type].sentOffer = false;
        }
    }

    var onFinish = function(data) {
        hideMask();
        if (data.err_code == 0) {
            exited = true;
            alertBox(c.msg.ALERT, c.msg.FINISHED, function() {
                interviewRoom.go("interview");  
            })
        }
        else if (data.err_code == c.err.ERR_NO_INTERVIEW[0] || 
            data.err_code == c.err.ERR_NOPRIV[0]) {
            interviewRoom.go('interview');
        }
        else {
            errorBox(c.msg.ALERT_ERROR, getErrMsg(data.err_code));
        }
    }

    var connect = function() {
        setTimeout(function() {
            if (!loginned)
                showMask(c.msg.CONNECTING);
        }, 1000);

        try {
            connWs = new WebSocket(c.scs_url);
        } catch (e) {
            print('Could not connect to server', e);
            return;
        }

        connWs.onopen = function() {
            print("connected");

            login();
        };

        connWs.onmessage = function(message) {
            //print("got message", message.data);

            var data = parseFrom(message.data);

            switch(data.type) {
                case "login":
                    onLogin(data);
                    break;

                case "offer":
                    onOffer(data);
                    break;

                case "answer":
                    onAnswer(data);
                    break;

                case "candidate":
                    onCandidate(data);
                    break;

                case "message":
                    onMessage(data);
                    break;

                case "live":
                    onLive(data);
                    break;

                case "leave":
                    onLeave(data);
                    break;

                case "finish":
                    onFinish(data);
                    break;

                default:
                    break;
            }
        };

        connWs.onerror = function(err) {
            print("got error", err);
        };

        connWs.onclose = function(e) {
            print("close socket", e);

            try {
                for (var i in utypes) {
                    ut = utypes[i];
                    if (ut != c.user_type) {
                        if (connRtc[ut].signalingState != 'closed')
                            connRtc[ut].close();
                    }
                }
            }
            catch(e) {

            }
            
            loginned = false;

            if (!closing) {
                clearState();

                refreshEnables();

                setTimeout(interviewRoom.connect(), 4000);
            }
        };
    }

    var clearState = function() {
        $('.state').text(c.msg.OFFLINE);
        $('#state_msg_p').text(c.msg.PATIENT_OFFLINE);
        $('#state_msg_d').text(c.msg.DOCTOR_OFFLINE);
        $('#state_msg_i').text(c.msg.INTERPRETER_OFFLINE);
    }

    var getUserName = function(ut) {
        switch(ut) {
            case "p":
                return c.msg.PATIENT;
            case "d":
                return c.msg.DOCTOR;
            case "i":
                return c.msg.INTERPRETER;
        }
    }

    /*********************************
    *         send message           *   
    *********************************/
    var newMessageNo = function() {
        no = c.user_type + "_";
        now = new Date();
        no += formatDate(now, "yyyyMMddhhmmss");

        return no;
    }

    var sendMessage = function() {
        // call api
        content = $('#chat_input').val();
        if (content != "") {
            no = $('#message_no').val();

            sendTo({
                type: "message",
                content: content,
                no: no
            });

            msg = {
                user_type: c.user_type,
                user_name: getUserName(c.user_type),
                content: content,
                no: no
            }

            insertMessage(msg, true);
        }
    }

    var scrollChatBottom = function(timer) {
        if (timer === undefined)
            timer = 1000;
        $('#chat_view').animate({
            scrollTop: $('#chat_view')[0].scrollHeight
        }, timer);
    }

    var messageHtml = function(msg, sending) {
        var html = "";
        html += '  <span class="user-name ' + (msg.user_type == c.user_type ? 'me' : '') + '">' + msg.user_name + '</span>';
        html += '  <div class="chat-message left">';
        if (sending) {
            html += '    <div class="sending"><span><i class="fa fa-spinner fa-spin"></i></span></div>';
        }
        html += '    <div class="message ' + (msg.user_type == c.user_type ? 'me' : '') + '">' + msg.content + '</div>';
        html += '    <div class="clear"></div>';
        html += '  </div>';

        return html;
    }

    var insertMessage = function(msg, sending) {
        var html = "";
        if ($('#msg_' + msg.no).length) {
            html = messageHtml(msg, sending);
            $('#msg_' + msg.no).html(html);
        }
        else {
            html = '<div class="message-wrapper" id="msg_' + msg.no + '">';
            html += messageHtml(msg, sending);
            html += '</div>';
            $('#chat_view #messages').append(html);
        }

        if (sending)
            scrollChatBottom();
    }

    /*********************************
    *         recording              *   
    *********************************/
    var initRecording = function(stream) 
    {
        if (stream == null || c.rcd_url == '')
            return;
        
        if (mediaRecorder && mediaRecorder.state == "recording")
            return;

        try {
            recordedBlobs = [];
            var q = c.qualities[c.quality];
            var options = {
                audioBitsPerSecond : 12800,
                videoBitsPerSecond : c.rcd_v_bitrate,
                mimeType: 'video/webm;codecs=vp9' };
            if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                print(options.mimeType + ' is not Supported');
                options.mimeType = 'video/webm;codecs=vp8';
                if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                    print(options.mimeType + ' is not Supported');
                    options.mimeType = 'video/webm';
                    if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                        print(options.mimeType + ' is not Supported');
                        options = {mimeType: ''};
                    }
                }
            }

            mediaRecorder = new MediaRecorder(stream, options);
        } catch (e) {
            mediaRecorder = null;
            print('Exception while creating MediaRecorder: ' + e  + '. mimeType: ' + options.mimeType);
            return;
        }
        print('Start record ', mediaRecorder, 'with options', options);
        mediaRecorder.onstop = function(event) {
            print('Recorder stopped: ', event);
            postRecord();
        };
        mediaRecorder.ondataavailable = function(event) {
            if (event.data && event.data.size > 0) {
                recordedBlobs.push(event.data);
            }
        };

        startRecording();
    }

    var startRecording = function(stop)
    {
        if (mediaRecorder)
        {
            if (stop)
            {
                if (mediaRecorder.state == "recording")
                    mediaRecorder.stop();
            }
            else {
                recordTimestamp = Date.parse((new Date()).toString());
                recordTimestamp += sys_now_offset;
                recordNo = 0;
            }
            mediaRecorder.start(10);
            print('MediaRecorder started', mediaRecorder);

            setTimeout("interviewRoom.startRecording(true)", c.rcd_fragment_interval);
        }
    }

    var postRecord = function()
    {
        print('Recorded Blobs: ', recordedBlobs.length);
        var superBuffer = new Blob(recordedBlobs, {type: 'video/webm'});

        var fd = new FormData();
        fd.append('interview_id', c.interview_id);
        fd.append('user_type', c.user_type);
        fd.append('timestamp', recordTimestamp);
        fd.append('no', recordNo);
        fd.append('data', superBuffer, 'data');
        $.ajax({
            type: 'POST',
            url: c.rcd_url + "api/v/p",
            data: fd,
            processData: false,
            contentType: false
        })
        .done(function(data) {
            completeUpload = true;
        })
        .fail(function( jqXHR, textStatus ) {
            completeUpload = true;
        });

        recordedBlobs = [];
        recordNo ++;
    }

    var stopRecording = function()
    {
        if (mediaRecorder && mediaRecorder.state == "recording") {
            completeUpload = false;
            mediaRecorder.stop();
        }
        else {
            completeUpload = true;
        }

        print('Stop record');
        mediaRecorder = null;
    }

    /*********************************
    *         resize layouts         *   
    *********************************/
    var resizeLayouts = function()
    {
        // resize article
        alert_height = $('.alert-waiting-doctor').height();
        c_width = $('article').width();
        c_height = $('article').height() - alert_height;
        v_max_width = c_width / 2 - 15;
        st_height = $('.row-big h4').height()
            + parseInt($('.row-big h4').css('margin-top'))
            + parseInt($('.row-big h4').css('margin-bottom'))
            + parseInt($('.row-big h4').css('padding-top'))
            + parseInt($('.row-big h4').css('padding-bottom'));
        l_height = $('.row-big h3').height()
            + parseInt($('.row-big h3').css('margin-top'))
            + parseInt($('.row-big h3').css('margin-bottom'))
            + parseInt($('.row-big h3').css('padding-top'))
            + parseInt($('.row-big h3').css('padding-bottom'));
        if (c.need_interpreter == "1") {
            s_height = $('.row-small').height()
                + parseInt($('.row-small').css('margin-top'))
                + parseInt($('.row-small').css('margin-bottom'))
                + parseInt($('.row-small').css('padding-top'))
                + parseInt($('.row-small').css('padding-bottom'));
            v_height = c_height - s_height - st_height - l_height - 10;
        } else {
            v_height = c_height - st_height - l_height - 10;
        }
        v_width = Math.floor(v_height / wh_rate);

        if (v_width > v_max_width) {
            v_width = v_max_width;
            v_height = Math.floor(v_width * wh_rate);
        }

        $('.video-big').height(v_height).width(v_width);

        $('article h4').each(function() {
            w = $(this).siblings('video').width();
            $(this).width(w);
            $(this).parent().find('.video-bar').width(w);
        });

        b_height = $('.row-big').height();
        s_height = $('.row-small').height();
        padding = parseInt((c_height - b_height - s_height) / 2);
        $('article .room-main')
            .css('padding-top', padding + "px")
            .css('padding-bottom', padding + "px");

        // resize aside
        a_height = $('aside').height() 
            - parseInt($('aside').css('padding-top'))
            - parseInt($('aside').css('padding-bottom'));

        $('aside > *').each(function() {
            if (!$(this).hasClass('chat-panel')) {
                a_height -= $(this).height() 
                    + parseInt($(this).css('margin-top'))
                    + parseInt($(this).css('margin-bottom'))
                    + parseInt($(this).css('padding-top'))
                    + parseInt($(this).css('padding-bottom'));
            }
        });

        panel = $('aside .chat-panel');
        a_height -= 
            parseInt(panel.css('margin-top'))
            + parseInt(panel.css('margin-bottom'))
            + parseInt(panel.css('padding-top'))
            + parseInt(panel.css('padding-bottom'));
        panel.height(a_height);

        if (!inited_chat_input)
            initChatInput();

        $('.interview-room').removeClass('transparent');
    }

    var initMicVolume = function ()
    {
        var getMicStatus = function() {
            var mic = true;
            if (myStream) {
                var audioTracks = myStream.getAudioTracks();
                for (var i in audioTracks) {
                    if (!audioTracks[i].enabled)
                        mic = false;
                }
            }
            return mic; 
        }
        var refreshMicButton = function(mic) {
            if (mic)
                $('.btn-mic').addClass('ln-icon-mic').removeClass('ln-icon-mic-mute');
            else
                $('.btn-mic').addClass('ln-icon-mic-mute').removeClass('ln-icon-mic');  
        }

        refreshMicButton(getMicStatus());

        $('.btn-mic').click(function() {
            mic = getMicStatus();
            if (mic)
                mic = false;
            else 
                mic = true;

            if (myStream) {
                var audioTracks = myStream.getAudioTracks();
                for (var i in audioTracks) {
                    audioTracks[i].enabled = mic;
                }
            }

            refreshMicButton(getMicStatus());
        });

        var vol = 0;
        for (var i in videos) {
            if (vol < $(videos[i]).prop('volume'))
                vol = $(videos[i]).prop('volume');
        }

        $('.btn-volume .slider').slider({
            orientation: "vertical",
            range: "min",
            min: 0,
            max: 1,
            step: 0.01,
            value: vol,
            slide: function (event, ui) {
                for (var i in videos) {
                    $(videos[i]).prop('volume', ui.value);
                }

                $('.btn-volume i')
                    .removeClass('ln-icon-volume-low')
                    .removeClass('ln-icon-volume-high')
                    .removeClass('ln-icon-volume-medium')
                    .removeClass('ln-icon-volume')
                if (ui.value == 0)
                    $('.btn-volume i').addClass('ln-icon-volume');
                else if (ui.value < 0.3) 
                    $('.btn-volume i').addClass('ln-icon-volume-low');
                else if (ui.value < 0.7) 
                    $('.btn-volume i').addClass('ln-icon-volume-medium');
                else 
                    $('.btn-volume i').addClass('ln-icon-volume-high');
            }
        });

    }

    var initChatInput = function()
    {
        var $mirror, $ta, $win, active, adjust, append, borderBox, boxOuter, copyStyle, forceAdjust, heightValue, initMirror, maxHeight, minHeight, minHeightValue, mirror, mirrorInitStyle, mirrored, resize, ta, taStyle, text;
        // text input
        $ta = $('#chat_input');
        ta = $ta[0];

        initMirror = function() {
            var mirrorStyle, mirrored, taStyle;
            mirrorStyle = mirrorInitStyle;
            mirrored = ta;
            taStyle = getComputedStyle(ta);
            for(var i in copyStyle) {
                val = copyStyle[i];
                mirrorStyle += val + ':' + taStyle.getPropertyValue(val) + ';';
            }
            mirror.setAttribute('style', mirrorStyle);
        };
        adjust = function() {
            var active, mirrorHeight, overflow, taComputedStyleWidth, taHeight, width;
            taHeight = void 0;
            taComputedStyleWidth = void 0;
            mirrorHeight = void 0;
            width = void 0;
            overflow = void 0;
            if (mirrored !== ta) {
                initMirror();
            }
            if (!active) {
                active = true;
                mirror.value = ta.value + append;
                mirror.style.overflowY = ta.style.overflowY;
                taHeight = ta.style.height === '' ? 'auto' : parseInt(ta.style.height, 10);
                taComputedStyleWidth = getComputedStyle(ta).getPropertyValue('width');
                if (taComputedStyleWidth.substr(taComputedStyleWidth.length - 2, 2) === 'px') {
                    width = parseInt(taComputedStyleWidth, 10) - boxOuter.width;
                    mirror.style.width = width + 'px';
                }
                mirrorHeight = mirror.scrollHeight;
                if (mirrorHeight > maxHeight) {
                    mirrorHeight = maxHeight;
                    overflow = 'scroll';
                } else if (mirrorHeight < minHeight) {
                    mirrorHeight = minHeight;
                }
                mirrorHeight += boxOuter.height;
                if (mirrorHeight < 24) {
                    mirrorHeight = 24;
                }
                ta.style.overflowY = overflow || 'hidden';
                if (taHeight !== mirrorHeight) {
                    ta.style.height = mirrorHeight + 'px';
                }
                adjustMessages();
                active = false;
            }
        };
        adjustMessages = function() {
            taheight = parseInt(ta.style.height);
                    
            ct_height = $('.chat-panel').height()
                + parseInt($('.chat-panel').css('padding-top'))
                + parseInt($('.chat-panel').css('padding-bottom'));
            $('.chat-container').height(ct_height - taheight 
                - parseInt(getComputedStyle(ta).getPropertyValue('padding-top'))
                - parseInt(getComputedStyle(ta).getPropertyValue('padding-top')) - 15);
        };
        forceAdjust = function() {
            var active;
            active = false;
            adjust();
            adjustMessages();
        };
        $ta.css({
            'overflow': 'hidden',
            'overflow-y': 'hidden',
            'word-wrap': 'break-word',
            'max-height': '88px'
        });
        text = ta.value;
        ta.value = '';
        ta.value = text;
        append = '';
        $win = $(window);
        mirrorInitStyle = 'position: absolute; top: -999px; right: auto; bottom: auto;' + 'left: 0; overflow: hidden; -webkit-box-sizing: content-box;' + '-moz-box-sizing: content-box; box-sizing: content-box;' + 'min-height: 0 !important; height: 0 !important; padding: 0;' + 'word-wrap: break-word; border: 0;';
        $mirror = $('<textarea tabindex="-1" ' + 'style="' + mirrorInitStyle + '"/>').data('elastic', true);
        mirror = $mirror[0];
        taStyle = getComputedStyle(ta);
        resize = taStyle.getPropertyValue('resize');
        borderBox = taStyle.getPropertyValue('box-sizing') === 'border-box' || taStyle.getPropertyValue('-moz-box-sizing') === 'border-box' || taStyle.getPropertyValue('-webkit-box-sizing') === 'border-box';

        if (!borderBox) {
            boxOuter = {
                width: 0,
                height: 0
            };
        } else {
            boxOuter = {
                width: parseInt(taStyle.getPropertyValue('border-right-width'), 10) + parseInt(taStyle.getPropertyValue('padding-right'), 10) + parseInt(taStyle.getPropertyValue('padding-left'), 10) + parseInt(taStyle.getPropertyValue('border-left-width'), 10),
                height: parseInt(taStyle.getPropertyValue('border-top-width'), 10) + parseInt(taStyle.getPropertyValue('padding-top'), 10) + parseInt(taStyle.getPropertyValue('padding-bottom'), 10) + parseInt(taStyle.getPropertyValue('border-bottom-width'), 10)
            };
        }
        minHeightValue = parseInt(taStyle.getPropertyValue('min-height'), 10);
        heightValue = parseInt(taStyle.getPropertyValue('height'), 10);
        minHeight = Math.max(minHeightValue, heightValue) - boxOuter.height;
        maxHeight = parseInt(taStyle.getPropertyValue('max-height'), 10);
        mirrored = void 0;
        active = void 0;
        copyStyle = ['font-family', 'font-size', 'font-weight', 'font-style', 'letter-spacing', 'line-height', 'text-transform', 'word-spacing', 'text-indent'];
        if ($ta.data('elastic')) {
            return;
        }
        maxHeight = maxHeight && maxHeight > 0 ? maxHeight : 9e4;
        if (mirror.parentNode !== document.body) {
            $(document.body).append(mirror);
        }
        $ta.css({
            'resize': resize === 'none' || resize === 'vertical' ? 'none' : 'horizontal'
        }).data('elastic', true);

        /*
         * initialise
         */

        // init form
        $('#input_bar').on('submit', function() {
            $('#message_no').val(newMessageNo());
            sendMessage();
        });
        $('#input_bar').ajaxForm({
            dataType : 'json',
            success: function(res, statusText, xhr, form) {
                try {
                    if (res.err_code == 0)
                    {
                        $('#chat_input').val('');
                        $('#chat_input')[0].oninput();
                        return;
                    }
                    else if (r.err_code == c.err.ERR_NOPRIV[0]) {
                        interviewRoom.go('interview');
                    }
                    else {
                        errorBox(c.msg.ALERT_ERROR, res.err_msg);
                    }
                }
                finally {
                }
            }
        }); 

        $('#chat_input').on('keydown', function(e) {
            if (e.keyCode == 13 && e.altKey == false && e.ctrlKey == false && e.shiftKey == false) { // enter
                e.preventDefault();
                $('#input_bar').submit();
            }
            else if (e.keyCode == 27) { // esc
                $('#chat_input').val('');
                $('#chat_input')[0].oninput();
            }
        });

        if ('onpropertychange' in ta && 'oninput' in ta) {
            ta['oninput'] = ta.onkeyup = adjust;
        } else {
            ta['oninput'] = adjust;
        }
        $win.bind('resize', forceAdjust);
        adjust();
    }

    return {
        init: function(config) {
            c = $.extend(defaults, config);
            if (c.ice_servers){
                connConfig = { iceServers: c.ice_servers };
            }

            clearState();

            openMyMedia();

            connect();

            $('.btn-start').click(function() {
                quality = $('#quality').val();
                interviewRoom.go("interview/room/" + c.interview_id + "/" + quality 
                    + "?" + Math.round(Math.random() *100));
            });

            $('.btn-finish').click(function() {
                confirmBox(c.msg.CONFIRM_FINISH_TITLE, c.msg.CONFIRM_FINISH, 
                    function() {
                        finish();
                    });
            });

            $('.btn-exit').click(function() {
                exited = true;
                interviewRoom.go("interview/");
            });

            $('#quality').change(function() {
                quality = $('#quality').val();
                interviewRoom.go("interview/room/" + c.interview_id + "/" + quality  
                    + "?" + Math.round(Math.random() *100));
            });

            $(window).resize(function() {
                w_height = $(window).height();
                $('.interview-room').height(w_height);

                setTimeout(interviewRoom.resizeLayouts, 1);
            });
            $(window).resize();

            // init messages
            for (var i in c.messages) {
                insertMessage(c.messages[i]);
            }
            scrollChatBottom(1);

            // clock
            interviewRoom.clock();

            initMicVolume();

            intCheckStatus = setInterval(function() {
                interviewRoom.checkStatus();
            }, 30 * 1000);
        },
        start: start,
        reoffer: reoffer,
        resizeLayouts: resizeLayouts,
        connect: connect,
        live: live,
        refreshWaitingDoctor: refreshWaitingDoctor,
        go: go,
        clock: function() {
            var now = Date.parse((new Date()).toString());
            sys_now = now + sys_now_offset;
            now = new Date();
            now.setTime(sys_now);
            $('#clock').text(formatDate(now, "HH:mm:ss"));

            setTimeout(interviewRoom.clock, 1000);
        },
        openMyMedia: openMyMedia,
        startRecording: startRecording,
        checkStatus: checkStatus
    }
}();