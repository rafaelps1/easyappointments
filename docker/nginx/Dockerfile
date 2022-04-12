# Webdevops
# https://dockerfile.readthedocs.io/en/latest/content/DockerImages/dockerfiles/php-nginx.html

################################################################
## First stage. Build NPM dependencies
################################################################
FROM node:12 as npm_builder

COPY . /var/www/html
# COPY package.json gulpfile.js application/ /var/www/html/

WORKDIR /var/www/html

RUN npm install
# RUN npm run build

################################################################
## Second stage, mostly copied from your original Dockerfile
################################################################
FROM webdevops/php-nginx:7.4-alpine

COPY --from=npm_builder /var/www/html /app
