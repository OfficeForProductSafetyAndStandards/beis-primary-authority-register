# beispar/db:latest

FROM postgres:13.14

ARG DATABASE_FILE='./db-dump-production-seed.sql'

COPY $DATABASE_FILE /tmp/db-dump-production-seed.sql

# Ensure that the on error flag is disabled at the top of the seed database import script.
ARG ON_ERROR='\unset ON_ERROR_STOP'
RUN echo "${ON_ERROR}\n\n" > /docker-entrypoint-initdb.d/db-dump-production-seed.sql \
    && cat /tmp/db-dump-production-seed.sql >> /docker-entrypoint-initdb.d/db-dump-production-seed.sql


