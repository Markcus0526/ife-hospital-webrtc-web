# IFE-HOSPITAL-WEBRTC-WEB
---

<img width="1200" height="800" alt="Hospital" src="https://github.com/user-attachments/assets/8ae3e3ac-2397-4f1b-9d15-17a64e78596c" />

--- 

<THINKING>
I inspected the repository and key files to learn how the project is structured and how to run it.

What I gathered:
- Project purpose: a WebRTC-based telemedicine platform for real-time video diagnostics (doctor, patient, interpreter).
- Main components:
  - www/ — PHP web application (MVC framework). Entry: www/index.php. Configuration: www/config.inc and many bundled libraries/plugins (PayPal SDK, PHPExcel, TCPDF, etc.).
  - scs/ — Node.js Signaling & Chat server (WebSocket) for WebRTC signaling. Entry: scs/server.js. Config: scs/config.js. package.json lists server dependencies.
  - recording/ — Recording web app and integration; entry: recording/index.php and recording/config.inc. Uses bundled ffmpeg binaries in recording/resource/ffmpeg.
  - database/teleclinic.sql — MySQL schema + data dump to create the teleclinic DB.
  - environment/apache2/teleclinic.conf — example Apache VirtualHost for deploying the web app over HTTPS.
  - turn/, resource/service scripts and init files for system services are present (for production).
- Important runtime requirements discovered:
  - PHP (minimum from sysconfig: 5.4.7) — many PHP files under www/ and recording/.
  - MySQL (database dump present).
  - Node.js & npm for scs (package.json).
  - ffmpeg for recording (binaries included, config points to FFMPEG path).
  - TLS certificates (several server.crt/.key files included; replace with proper certs).
  - Ports: 443 for HTTPS & WSS, TURN/STUN ports (if using TURN), possible 3478 for TURN.
- I reviewed config samples (www/config.inc, recording/config.inc, scs/config.js) that show what must be changed to match your environment (DB host, credentials, SCS_URL, ICE_SERVERS, API endpoints).

Key README sections I decided to include and why:
- Project overview & architecture: to quickly explain what each component does.
- Quickstart (minimum steps): a small, runnable path to get the system up locally or on a server.
- Detailed setup: DB import, configuration edits (what to change & where), starting services (scs, Apache), testing ffmpeg and SCS.
- Deployment hints: Apache VirtualHost, systemd/service hints, TLS, running scs as a background service.
- Troubleshooting: common issues (ports, ffmpeg path, permissions, config mistakes).
- Security & notes: many example config files contain credentials — alert maintainers to rotate and never commit secrets.
- Contributing & License: brief pointers.

Structure & style choices:
- Short introduction and architecture diagram (text) to make the stack clear at a glance.
- Step-by-step commands for the most common use-case (Ubuntu/Debian) — these are pragmatic and copy-paste ready.
- Emphasize files to edit and what values matter (DB, SCS URL, ICE servers, ffmpeg path).
- Keep examples short and actionable; avoid exposing secrets — call out where secrets exist and that they must be changed.
- Provide references to service scripts included under resource/service and scs/service to help production deployments.

I will now produce the final README in Markdown following these decisions.
</THINKING>

<SUMMARY>
- Created a focused, copy-paste-ready README for the telemedicine WebRTC system (PHP web app + Node signaling + recording).
- Included a quickstart (DB import, config edits, start scs, enable web app) and detailed setup instructions for production.
- Added deployment hints (Apache config), troubleshooting tips, and security guidance about changing credentials and certificates.
</SUMMARY>

<README>
# IFE-HOSPITAL (Teleclinic WebRTC)

<em>Real-time Video Diagnostics Web System between Doctor, Patient and Interpreter based on WebRTC</em>

<img src="https://img.shields.io/badge/Express-000000.svg?style=flat-square&logo=Express&logoColor=white" alt="Express"> <img src="https://img.shields.io/badge/JSON-000000.svg?style=flat-square&logo=JSON&logoColor=white" alt="JSON"> <img src="https://img.shields.io/badge/npm-CB3837.svg?style=flat-square&logo=npm&logoColor=white" alt="npm"> <img src="https://img.shields.io/badge/Bower-EF5734.svg?style=flat-square&logo=Bower&logoColor=white" alt="Bower"> <img src="https://img.shields.io/badge/Composer-885630.svg?style=flat-square&logo=Composer&logoColor=white" alt="Composer"> <img src="https://img.shields.io/badge/Grunt-FAA918.svg?style=flat-square&logo=Grunt&logoColor=white" alt="Grunt"> <img src="https://img.shields.io/badge/JavaScript-F7DF1E.svg?style=flat-square&logo=JavaScript&logoColor=black" alt="JavaScript"> <img src="https://img.shields.io/badge/GNU%20Bash-4EAA25.svg?style=flat-square&logo=GNU-Bash&logoColor=white" alt="GNU%20Bash"> <br>
<img src="https://img.shields.io/badge/FFmpeg-007808.svg?style=flat-square&logo=FFmpeg&logoColor=white" alt="FFmpeg"> <img src="https://img.shields.io/badge/MySQL-4479A1.svg?style=flat-square&logo=MySQL&logoColor=white" alt="MySQL"> <img src="https://img.shields.io/badge/XML-005FAD.svg?style=flat-square&logo=XML&logoColor=white" alt="XML"> <img src="https://img.shields.io/badge/Less-1D365D.svg?style=flat-square&logo=Less&logoColor=white" alt="Less"> <img src="https://img.shields.io/badge/PHP-777BB4.svg?style=flat-square&logo=PHP&logoColor=white" alt="PHP"> <img src="https://img.shields.io/badge/Bootstrap-7952B3.svg?style=flat-square&logo=Bootstrap&logoColor=white" alt="Bootstrap"> <img src="https://img.shields.io/badge/CSS-663399.svg?style=flat-square&logo=CSS&logoColor=white" alt="CSS">

---

Table of Contents
- [Overview](#overview)
- [Repository layout](#repository-layout)
- [Prerequisites](#prerequisites)
- [Quickstart (minimum to run)](#quickstart-minimum-to-run)
- [Detailed setup](#detailed-setup)
  - [Database](#database)
  - [Web app (www)](#web-app-www)
  - [Signaling server (scs)](#signaling-server-scs)
  - [Recording service](#recording-service)
  - [Running TURN/STUN (optional)](#running-turnstun-optional)
  - [Production: Apache VirtualHost example](#production-apache-virtualhost-example)
- [Testing & verification](#testing--verification)
- [Troubleshooting](#troubleshooting)
- [Security notes](#security-notes)
- [Development & build](#development--build)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

This repository implements a telemedicine platform that supports real-time video/audio, chat and recording between doctors, patients and interpreters using WebRTC. The system is split into three primary components:

- PHP web application (www/) — primary frontend and REST API endpoints for administration and user workflows.
- Node.js Signaling & Chat Server (scs/) — WebSocket-based signaling server that relays SDP offers/answers/candidates and in-room messages.
- Recording service (recording/) — a web component used for recording WebRTC sessions (uses ffmpeg).

A MySQL schema is provided (database/teleclinic.sql). Example Apache configuration is available at environment/apache2/teleclinic.conf.

Architecture (simplified)
- Browser (client) <-> Frontend (www) (HTTPS)
- Browser WebRTC <-> Peer (P2P / TURN) for media
- Browser <-> scs (WSS) for signaling and chat
- recording service receives media or coordinates recording via ffmpeg

---

## Repository layout (high-level)

- README.md — (this file)
- LICENSE — MIT
- database/teleclinic.sql — MySQL schema & initial data
- www/ — PHP web application (MVC)
  - index.php, config.inc, application/, core/, resource/, package.json, etc.
- scs/ — WebSocket signaling server (Node.js)
  - server.js, config.js, package.json
- recording/ — recording web app and ffmpeg binaries
- environment/apache2/teleclinic.conf — example Apache VirtualHost
- turn/ — TURN server archives / account.txt (not pre-built)

See the full tree in the repo for all included plugins and resources.

---

## Prerequisites

These are the typical requirements to run the platform.

System
- Linux (Ubuntu / Debian recommended for the example commands)
- Root/privileged user to configure services / Apache / ports

Software
- PHP >= 5.4.7 (sysconfig declares MIN_PHP_VER '5.4.7'; modern PHP is recommended)
  - Required PHP extensions: mysqli, mbstring, openssl, zip, gd (typical LAMP stack)
- MySQL / MariaDB (create `teleclinic` database and import SQL)
- Apache2 (or any PHP-capable web server)
- Node.js (>= 0.10.0 per package.json; use a maintained LTS version)
- npm (to install scs dependencies)
- ffmpeg and ffprobe (the project includes ffmpeg/ffprobe under recording/resource/ffmpeg; you may use system packages)
- Optional: TURN server (coturn) for NAT traversal in restrictive networks
- Open ports: 443 (HTTPS / WSS), 3478 (TURN), 80 (optional; HTTP redirect)

Note: The repo contains example certs and example credentials. Replace them before production use.

---

## Quickstart (minimum to run)

This section shows the minimal steps to get a working setup for development/testing.

1. Clone the repository:
```bash
git clone https://github.com/Markcus0526/ife-hospital-webrtc-web.git teleclinic
cd teleclinic
```

2. Import the database:
```bash
# create DB and import
mysql -u root -p
CREATE DATABASE teleclinic CHARACTER SET utf8 COLLATE utf8_general_ci;
EXIT

mysql -u root -p teleclinic < database/teleclinic.sql
```

3. Configure the PHP web app:
- Edit `www/config.inc` and set:
  - DB_HOSTNAME, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT
  - FRONTEND_URL (your site URL)
  - SCS_URL (set to your scs WSS endpoint, e.g. wss://your.domain:443)
  - ICE_SERVERS (STUN/TURN list)

4. Configure and start the Signaling Server:
```bash
cd scs
# optional: edit scs/config.js to set api_prefix, ssl cert paths, port
npm install
# start in foreground for testing
node server.js
# or use server.min.js or a process manager (pm2/systemd) in production
```

5. Deploy web app to Apache docroot:
- Place the `www` folder under your web root (e.g. /var/www/teleclinic or symlink).
- Use the included example `environment/apache2/teleclinic.conf` for HTTPS VirtualHost (replace certificate paths).
- Restart Apache and ensure PHP is configured.
- Visit your FRONTEND_URL.

6. Recording:
- Configure `recording/config.inc` (DB, ffmpeg path FFMPEG & FFPROBE paths).
- Deploy `recording` under its own HTTPS host (e.g. record.example.com) and ensure `RECORDING_API` in `www/config.inc` points to that host.

---

## Detailed setup

### Database
- Import the SQL dump as shown above.
- The `database/teleclinic.sql` contains tables and seed data.
- If you intend to run multiple instances, review `www/core/sql/*` for migrations & conversions.

### Web app (www)
- Primary configuration file: `www/config.inc`.
  - Update database credentials, mail/SMS API keys, PayPal/CHINAPAY credentials, `SCS_URL`, `ICE_SERVERS` and `FRONTEND_URL`.
  - Default language and many timeouts are configured here (see comments).
- Document root: point Apache's VirtualHost DocumentRoot to the `www` directory.
- Vendor code: repository includes many libraries (PayPal SDK, PHPExcel, TCPDF). Composer is not strictly required but you may use it if you prefer.

Permissions
- Ensure `www/data`, `www/log`, `www/data/avartar` (and similar runtime directories) are writable by the web server user (www-data/apache).

### Signaling Server (scs)
- Edit `scs/config.js`:
  - `ssl`, `port`, `ssl_key`, `ssl_cert`, `api_prefix` (the API prefix your PHP server exposes).
- Install dependencies and run:
```bash
cd scs
npm install
node server.js
```
- For production run as a service:
  - Use the provided scripts in `scs/service/*` (Ubuntu/RedHat) or create a systemd unit to run `node server.js` as a managed service.
  - Example: run under `pm2` or `systemd` to auto-restart.

### Recording service
- `recording/config.inc`:
  - Set DB credentials, FFMPEG and FFPROBE paths (FFMPEG & FFPROBE included at recording/resource/ffmpeg but you can use system-installed).
- The recording app expects to be hosted on HTTPS (recording API endpoints referenced by web app).

### TURN/STUN
- In restrictive NAT scenarios a TURN server is necessary.
- The repository includes sources/archives under `turn/` and a sample `turn/account.txt`. For production use, set up coturn and update `ICE_SERVERS` in `www/config.inc` and `recording/config.inc`.

### Production: Apache VirtualHost example
- See `environment/apache2/teleclinic.conf` for an example SSL-enabled virtual host pointing to `/www/teleclinic`.
- Replace `SSLCertificateFile` and `SSLCertificateKeyFile` with your CA-signed certs.
- Ensure `SCS_URL` (in www/config.inc) uses `wss://` with a host that matches scs certificate.

---

## Testing & verification

1. Verify backend:
```bash
# test DB connectivity (example in PHP CLI)
php -r "require 'www/core/global.php'; echo 'OK';"
```

2. Test scs:
```bash
node scs/server.js
# should print "Teleclinic SC(Signaling & Chat) Server started..." per config
```

3. Test ffmpeg:
```bash
/your/path/to/ffmpeg -version
# Or use the bundled binary: recording/resource/ffmpeg/ffmpeg -version
```

4. Web UI:
- Open your FRONTEND_URL in a browser and try a demo call (depends on app data and accounts).
- Check browser console for WSS connection to `SCS_URL` and ICE connectivity.

---

## Troubleshooting

- WSS connection fails:
  - Ensure scs is running and its certificate matches the host used by clients.
  - Check firewall (port 443) and proxy configuration.

- STUN/TURN issues (media not connecting):
  - Verify ICE_SERVERS JSON is correct (format and reachable).
  - If behind strict NAT, deploy TURN and include TURN credentials in ICE_SERVERS.

- ffmpeg recording failures:
  - Confirm `FFMPEG` and `FFPROBE` paths in `recording/config.inc`.
  - Run ffmpeg manually to ensure execution permissions.

- Permission errors:
  - Ensure PHP runtime directories under www/ are writable by web server user.

- Database connection errors:
  - Double-check host/port/user/password in `www/config.inc` and that MySQL allows connections from the host.

- Long startup logs or “already listening”:
  - Ensure ports are not used by other services (e.g. existing Apache or another Node process).

---

## Security notes

- Many configuration files in this repo contain example credentials and certificate files (e.g. `www/config.inc`, `recording/config.inc`). NEVER leave default passwords/certificates in production.
- Replace example TLS certificates with CA-signed certs (Let's Encrypt or commercial CA).
- Rotate API keys, SMS keys, email creds, PayPal/CHINAPAY secrets before production.
- Move secret credentials to environment variables or protected configuration not checked into git if customizing.

---

## Development & build

- Frontend build tools:
  - `www/package.json` lists Grunt & frontend tooling. Use `npm install` then `grunt`/`grunt watch` where appropriate.
- Signaling server:
  - `scs/package.json` contains dependencies (ws, mysql, request). Use `npm install` in scs/ before running.
- Unit tests:
  - Some vendor packages include tests; project-wide tests are not included by default.

Example:
```bash
# scs
cd scs
npm install
node server.js

# www (optional build steps)
cd www
npm install
# install grunt cli if needed
npm install -g grunt-cli
grunt
```

---

## Contributing

- Please open issues for bugs or feature requests.
- For code contributions:
  - Fork the repo, create a feature branch, run and test locally, open a pull request with a clear description.
  - Keep secrets out of commits. If a config file must be changed for CI, provide a template with placeholders (do not commit secrets).

---

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file.

---

If you want, I can:
- Produce a sanitized sample `www/config.inc.sample` and `scs/config.js.sample` with placeholders for secrets.
- Create systemd unit examples for scs and recording.
- Add a quick "developer local" guide for running everything via Docker (if you prefer containerized steps).

