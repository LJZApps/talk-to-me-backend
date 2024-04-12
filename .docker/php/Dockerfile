ARG PHP_VERSION=fpm-alpine
FROM php:${PHP_VERSION} AS base
ARG PHP_VERSION

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        apcu \
        exif \
        intl \
        imagick \
        ldap \
        gd \
        mongodb \
        mysqli \
        opcache \
        pdo_mysql \
        soap \
        zip \
        ''

# Software 1
RUN apk update \
    && apk add --no-cache \
        bash \
        curl \
        dirmngr \
        git \
        gpg \
        gpg-agent \
        less \
        mysql-client \
        openssh-client \
        su-exec \
        supervisor

RUN wget -O phive.phar "https://phar.io/releases/phive.phar" \
    && wget -O phive.phar.asc "https://phar.io/releases/phive.phar.asc" \
    && gpg --keyserver hkps://keys.openpgp.org --recv-keys 0x6AF725270AB81E04D79442549D8A98B29B2D5D79 \
    && gpg --verify phive.phar.asc phive.phar \
    && rm phive.phar.asc \
    && chmod +x phive.phar \
    && mv phive.phar /usr/bin/phive

WORKDIR /code

RUN mkdir -m 0777 /.composer \
    && mkdir -m 0777 /.wp-cli

ADD php.ini /tmp/php.ini
ADD docker-entrypoint.sh /usr/local/sbin/docker-entrypoint.sh

RUN phive install --copy --target /usr/bin --trust-gpg-keys CBB3D576F2A0946F composer
RUN phive install --copy --target /usr/bin --trust-gpg-keys 51C67305FFC2E5C0 phpstan
RUN phive install --copy --target /usr/bin --trust-gpg-keys 12CE0F1D262429A5 psalm
RUN echo -e "y\n" | phive install --copy --target /usr/bin --trust-gpg-keys 31C7E470E2138192 phpcs
RUN phive install --copy --target /usr/bin --trust-gpg-keys 31C7E470E2138192 phpcbf
RUN phive install --copy --target /usr/bin --trust-gpg-keys E82B2FB314E9906E php-cs-fixer
RUN phive install --copy --target /usr/bin --trust-gpg-keys 4AA394086372C20A phpunit@^9.0.0

ENTRYPOINT ["/usr/local/sbin/docker-entrypoint.sh"]
CMD ["php-fpm"]

FROM base AS base-cron

ENV SUPERCRONIC_URL=https://github.com/aptible/supercronic/releases/download/v0.1.12/supercronic-linux-amd64 \
    SUPERCRONIC=supercronic-linux-amd64 \
    SUPERCRONIC_SHA1SUM=048b95b48b708983effb2e5c935a1ef8483d9e3e

RUN curl -fsSLO "$SUPERCRONIC_URL" \
 && echo "${SUPERCRONIC_SHA1SUM}  ${SUPERCRONIC}" | sha1sum -c - \
 && chmod +x "$SUPERCRONIC" \
 && mv "$SUPERCRONIC" "/usr/local/bin/${SUPERCRONIC}" \
 && ln -s "/usr/local/bin/${SUPERCRONIC}" /usr/local/bin/supercronic

ADD cron /tmp/cron
ADD cron_cron.sh /cron_cron.sh
ADD supervisord.conf /etc/supervisord.conf

CMD ["supervisord"]

FROM base AS base-debug

RUN install-php-extensions \
        xdebug \
        ''
FROM base-cron AS base-cron-debug

RUN install-php-extensions \
        xdebug \
        ''
