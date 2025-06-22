FROM dpage/pgadmin4:latest

USER root

ARG POSTGRES_USER
ENV POSTGRES_USER=${POSTGRES_USER}

RUN mkdir -p /var/lib/pgadmin/storage/${POSTGRES_USER}_pgadmin.com/

COPY config/pgpass /var/lib/pgadmin/storage/${POSTGRES_USER}_pgadmin.com/pgpass
COPY config/servers.json /pgadmin4/servers.json

RUN chown -R pgadmin /var/lib/pgadmin
