#!/bin/bash

export HOST_ID=$(ip -f inet addr show eth0 | sed -En -e 's/.*inet ([0-9.]+).*/\1/p' | sed -En -e 's/\.\d+$/.1/p')
sed "s/xdebug.client_host=.*$/xdebug.client_host=${HOST_ID}/" /tmp/php.ini > /usr/local/etc/php/php.ini 

if [ -n "$USER" ]; then
	IFS=';' read -r -a parsed_user <<< "$USER" ; unset IFS
	for user_account in ${parsed_user[@]}
	do
		IFS=':' read -r -a tab <<< "$user_account" ; unset IFS
		user_login=${tab[0]}
		user_uid=${tab[1]}
		user_gid=${tab[2]}

        echo "user_login: $user_login"
        echo "user_uid: $user_uid"
        echo "user_gid: $user_gid"

		mkdir /home/$user_login
		addgroup \
            -g $user_gid \
            $user_login
		adduser \
			-s /bin/sh \
			-h /home/$user_login \
            -D \
			-G $user_login \
            -u $user_uid \
			$user_login
		chown -R $user_login:$user_login /home/$user_login
	done
fi

export HOME=/home/$user_login
chown $user_login:$user_login /proc/self/fd/2

su-exec $user_login:$user_login "$@"
