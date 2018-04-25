# Set nginx base image
FROM nginx:latest

# Copy custom configuration file from the current directory
COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./api.conf /etc/nginx/conf.d/api.conf
COPY ./app.conf /etc/nginx/conf.d/app.conf

