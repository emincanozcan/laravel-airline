# npm

FROM node:12 AS npm-builder

WORKDIR /var/www/html

COPY package.json package-lock.json webpack.mix.js /var/www/html/
COPY resources /var/www/html/resources/
COPY public /var/www/html/

RUN npm ci
RUN npm run production


FROM nginx:1.17

COPY .docker/nginx/nginx_template_prod.conf /etc/nginx/conf.d/default.conf
COPY --chown=www-data --from=npm-builder /var/www/html/public/ /var/www/html/public/
COPY --chown=www-data . /var/www/html/