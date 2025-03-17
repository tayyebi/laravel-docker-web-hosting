FROM alpine:3.6

RUN apk add --no-cache nginx-mod-http-lua

RUN mkdir -p /run/nginx /var/tmp/nginx/client_body

RUN adduser -S -D -H www-data || true && \
chown -R www-data:www-data /run/nginx /var/lib/nginx /var/log/nginx /var/tmp/nginx/client_body

COPY ./nginx.conf /etc/nginx/nginx.conf

CMD ["nginx"]
