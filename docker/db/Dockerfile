FROM postgres:bullseye
ENV POSTGRES_USER=root
ENV POSTGRES_PASSWORD=root
RUN chmod 777 /var/lib/postgresql/data
EXPOSE 5432
CMD ["apachectl", "-D", "FOREGROUND"]