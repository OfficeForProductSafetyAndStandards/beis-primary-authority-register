# Docker Development Environment

Docker is used by CircleCI and all testing environments as well as being available to run locally for developers.

## Building the docker image

Before pushing the docker image check that it builds locally:
```
docker build --no-cache -t beispar/web:latest ./web
docker build --no-cache -t beispar/db:latest ./db
```

Run `docker image ls` to check that the image was correctly built.

Once the image builds you can execute commands inside the container to confirm all the components run as expected.
```
docker exec -it beispar/web:latest /bin/bash
docker exec -it beispar/db:latest /bin/bash
```

### Build a specific stage

The docker images are split into multi-stage builds:

- production (only the assets needed in prod)
- dev (only the assets needed for local dev)
- test (all the assets for CI/CD)

By default, the entire image will be built which includes everything needed to run tests. If a lighter docker image is required a previous step can be specified in the build process:
```
docker build --no-cache --target {STAGE} -t beispar/web:latest ./web
```

## Pushing a new docker image

To publish a new version of the Docker image run the push.sh script in the appropriate docker folder:
```
./web/push.sh -t v1.0.0
./db/push.sh -t v1.0.0
```

You must have access to the central docker hub to push images to `beis-par` and login first with `docker login`.
