### Manual Installation

1. Please follow this guide to install [ubuntu 22.02 with Nginx, MySQL, PHP-FPM](https://clivern.com/installing-nginx-mysql-php-on-ubuntu-22-04/)

2. if you deployed helium to this path `/var/www/project/` you need to create two systemd services.

3. To install PHP packages optimized for production.

```zsh
$ make prod_composer
```

4. To migrate the database, you can use the following command

```zsh
$ make migrate
```

5. The `watcher` systemd service.

```
$ nano /etc/systemd/system/watcher.service
```

```
[Unit]
Description=Helium Watcher
After=network.target

StartLimitIntervalSec=500
StartLimitBurst=5

[Service]
User=root
Group=www-data
WorkingDirectory=/var/www/project
Restart=on-failure
RestartSec=5s
Environment=LANG=en_US.UTF-8
Environment=LC_ALL=en_US.UTF-8
Environment=LC_LANG=en_US.UTF-8
ExecStart=php /var/www/project/bin/console watch
ExecReload=/bin/kill -s HUP $MAINPID
ExecStop=/bin/kill -s TERM $MAINPID
PrivateTmp=true

[Install]
WantedBy=multi-user.target
```

6. The `worker` systemd service.

```
$ nano /etc/systemd/system/worker.service
```

```
[Unit]
Description=Helium Worker %i
After=network.target

StartLimitIntervalSec=500
StartLimitBurst=5

[Service]
User=root
Group=www-data
WorkingDirectory=/var/www/project
Restart=on-failure
RestartSec=5s
Environment=LANG=en_US.UTF-8
Environment=LC_ALL=en_US.UTF-8
Environment=LC_LANG=en_US.UTF-8
ExecStart=php /var/www/project/bin/console messenger:consume async
ExecReload=/bin/kill -s HUP $MAINPID
ExecStop=/bin/kill -s TERM $MAINPID
PrivateTmp=true

[Install]
WantedBy=multi-user.target
```

7. Then enable and start them

```
$ systemctl enable watcher
$ systemctl start watcher

$ systemctl enable worker@1
$ systemctl start worker@1
```

### Installation with Ansible (preferred)

You need to install `Python 3` locally and use a laptop that has `SSH` access to the production `Linux` server. Then please follow these steps:

1. Create a python virtual environment.

```zsh
$ python3 -m venv venv
$ source venv/bin/activate
```

2. Install `ansible`

```zsh
$ make config
```

3. Create `hosts.prod` from `hosts` file and replace `127.0.0.1` with the host IP.

4. Create `prod.vault.yml` with these configs.

```zsh
$ ansible-vault create prod.vault.yml
```

```yaml
install_mysql: true
root_username: root
root_password: R2ZBmTR6nED6a71AxeTO2DSck
app_db_name: helium
app_db_username: admin
app_db_password: R2ZBmTR6nED6a71AxeTO2UIok
allow_access_from: "127.0.0.1"

install_nginx: true

install_php: true
php_version: 8.1 # or 7.4 on Ubuntu 20.04

install_composer: true

install_postfix: false
postfix_hostname: helium
smtp_sasl_security_options: noanonymous
smtp_sasl_auth_enable: 'yes'
smtp_use_tls: 'yes'
smtp_server: smtp.gmail.com
smtp_port: 587
smtp_username: ''
smtp_password: ''
inet_interfaces: 127.0.0.1
```

`Postfix` is disabled but it can be enabled by setting `install_postfix: true` above and providing the SMTP Server, username and password. You can use your gmail account as an SMTP server by doing the following:

- The `SMTP server` will be `smtp.gmail.com` and port `587`
- The `SMTP username` is your gmail email address.
- The `SMTP password` can be generated after enabling two factor authentication. Please check this guide to generate the app password https://www.golinuxcloud.com/gmail-smtp-relay-server-postfix/.

5. Run ansible playbook to setup the server

```zsh
$ make prod
```

Here is an [article explaining the above steps in more details](https://clivern.com/installing-nginx-mysql-php-on-ubuntu-22-04/)


*To Deploy Helium Application:*

The following steps will require you to upload the code to a private github or gitlab repository, Add the host public key as a deploy key Then provide the repository git link.

1. Create `helium.vault.yml` with these configs.

```zsh
$ ansible-vault create helium.vault.yml
```

```yaml
app_user: appmgmt
app_group: appmgmt

app_version: 1.0.0
git_repo: git@github.com:$AUTHOR/$REPO.git

workers_counts: 1

hostname: helium.com

php_version: 8.1 # or 7.4 on Ubuntu 20.04

app_db_name: helium
app_db_username: admin
app_db_password: R2ZBmTR6nED6a71AxeTO2UIok

app_secret: 3999bf7e3a408f15942779447e01dd7a
app_timezone: UTC
app_env: prod
app_locale: en
messenger_transport: doctrine://default
mailer_dsn: sendmail://default
```

*Please not that `mailer_dsn` can be one of the following forms:*

```
# If Amazon Mailer
mailer_dsn: ses://ACCESS_KEY:SECRET_KEY@default?region=eu-west-1
mailer_dsn: ses+smtp://ACCESS_KEY:SECRET_KEY@default?region=eu-west-1

# If Mailchimp
mailer_dsn: mandrill://KEY@default
mailer_dsn: mandrill+smtp://USERNAME:PASSWORD@default

# If mailgun
mailer_dsn: mailgun://KEY:DOMAIN@default?region=us
mailer_dsn: mailgun+smtp://USERNAME:PASSWORD@default?region=us

# If Mailjet
mailer_dsn: mailjet+api://PUBLIC_KEY:PRIVATE_KEY@api.mailjet.com
mailer_dsn: mailjet+smtp://PUBLIC_KEY:PRIVATE_KEY@in-v3.mailjet.com

# If Postmark
mailer_dsn: postmark://ID@default

# If Sendgrid
mailer_dsn: sendgrid://KEY@default

# If Sendinblue
mailer_dsn: sendinblue+api://KEY@default
mailer_dsn: sendinblue+smtp://USERNAME:PASSWORD@default

# If Mailtrap
mailer_dsn: smtp://USERNAME:PASSWORD@smtp.mailtrap.io:2525?encryption=tls&auth_mode=login
```

2. Run ansible playbook to deploy `Helium` application.

```zsh
$ make helium
```