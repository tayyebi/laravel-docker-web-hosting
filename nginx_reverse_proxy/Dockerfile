FROM alpine:3.6

RUN apk add --no-cache nginx-mod-http-lua

# Create folder for PID file
RUN mkdir -p /run/nginx

CMD ["nginx"]
