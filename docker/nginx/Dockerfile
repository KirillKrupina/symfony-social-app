FROM nginx:alpine

COPY nginx.conf /etc/nginx/
COPY default.conf /etc/nginx/conf.d/

RUN apk update && apk upgrade && apk add --no-cache bash

ARG PHP_UPSTREAM_CONTAINER=php-fpm
ARG PHP_UPSTREAM_PORT=9000

RUN set -x ;\
    addgroup -g 82 -S www-data ;\
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

RUN set -x ;\
    ln -sf /dev/stdout /var/log/nginx/access.log ;\
    ln -sf /dev/stdout /var/log/nginx/project_access.log ;\
    ln -sf /dev/stdout /var/log/nginx/error.log ;\
    ln -sf /dev/stdout /var/log/nginx/project_error.log

# Set upstream conf and remove the default conf
RUN echo "upstream php-upstream { server ${PHP_UPSTREAM_CONTAINER}:${PHP_UPSTREAM_PORT}; }" > /etc/nginx/conf.d/upstream.conf

EXPOSE 80