# beispar/db:latest

FROM postgres:13.6

ARG DATABASE_FILE='../../../backups/db-dump-production-seed.sql'

COPY $DATABASE_FILE /docker-entrypoint-initdb.d/db-dump-production-seed.sql
