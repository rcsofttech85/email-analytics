FROM nginx:latest as prod
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

FROM prod as dev