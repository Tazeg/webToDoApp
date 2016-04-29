#-----------------------------------------------------------------------
# JeffProd - Web ToDo app
#-----------------------------------------------------------------------
# AUTHOR	: Jean-Francois GAZET
# WEB 		: http://www.jeffprod.com
# TWITTER	: @JeffProd
# MAIL		: jeffgazet@gmail.com
# LICENCE	: GNU GENERAL PUBLIC LICENSE Version 3, June 2007
#-----------------------------------------------------------------------

FROM ubuntu

MAINTAINER Jean-François GAZET <jeffgazet@gmail.com>

LABEL version="1"
LABEL description="Web ToDo App - Apache/PHP/SQLite"

ENV DEBIAN_FRONTEND noninteractive

RUN apt-get -y update && apt-get install -y \
apache2 \
php7.0 \
libapache2-mod-php7.0 \
php7.0-gd \
php7.0-json \
php7.0-sqlite \
php7.0-mysql \
php7.0-mcrypt \
php7.0-curl \
php7.0-xml \
mcrypt \
nano

RUN locale-gen fr_FR.UTF-8
RUN locale-gen en_US.UTF-8
RUN locale-gen de_DE.UTF-8

# for nano
ENV TERM xterm

# Apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
RUN rm /var/www/html/index.html
COPY ["www","/var/www/html"]
RUN chown -R www-data:www-data /var/www
RUN find /var/www -type d -exec chmod 775 {} +
RUN find /var/www -type f -exec chmod 664 {} +

# user host data dir
RUN mkdir /data
VOLUME ["/data"]
COPY ["entrypoint.sh", "/"]
RUN chmod +x /entrypoint.sh

EXPOSE 80

CMD /entrypoint.sh && /usr/sbin/apache2ctl -DFOREGROUND
