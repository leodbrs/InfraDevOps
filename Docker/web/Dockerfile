FROM debian:10
RUN apt-get update
RUN apt-get install -y \
                        apache2 \
                        php \
                        php-pgsql \
                        libapache2-mod-php \
                        && apt-get clean
VOLUME [ "/etc/apache2/sites-available/dvdrental.conf" , "/var/www/dvdrental/"]
RUN ln -s /etc/apache2/sites-available/dvdrental.conf /etc/apache2/sites-enabled
RUN rm /etc/apache2/sites-enabled/000-default.conf
RUN echo "www.dvdrental.fr 127.0.0.1" >> /etc/apache2/apache2.conf
EXPOSE 80
CMD ["apachectl", "-D", "FOREGROUND"]
