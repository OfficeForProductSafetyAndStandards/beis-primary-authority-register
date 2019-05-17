# Docker Development Environment

Docker is used by CircleCI and all testing environments as well as being available to run locally for developers.

## Building docker image

To build changes to the Docker image run the build script.
```
sh ./build.sh
```

Check that the new image tag has been created and check connect to the image to check that everything is good with it.
```
docker image ls
docker exec -it beispar/web:latest /bin/bash
```

When you're ready to tag you can either run the build script again with a version constraint:
```
sh ./build.sh -t [VERSION]
```
or tag the image you created previously:
```
docker tag beispar/web:latest beispar/web:[VERSION]
```

## Push changes to Docker hub

To push changes to the central dockerhub repository
```
docker push beispar/web:[VERSION]
```

You must have access to the central docker hub to push images to `beis-par` and login first with `docker login`.