FROM php:7.4-cli

# Install dependencies
RUN apt update && apt install -y \
    wget \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Set the working directory
WORKDIR /var/www/html

# Download and extract FileBrowser
RUN wget -q https://filebrowser.linuxforphp.net/files/filebrowser-8.0.3.zip && \
    unzip -q filebrowser-8.0.3.zip -d /tmp && \
    mv /tmp/filebrowser/* /var/www/html && \
    rm -rf filebrowser-8.0.3.zip /tmp/filebrowser

# Copy configuration files
COPY configuration.php /var/www/html/configuration.php

# Expose port 80
EXPOSE 80

# Run the PHP built-in web server as root
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]