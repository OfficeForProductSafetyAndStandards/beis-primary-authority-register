# Status

This is still **Work In Progress**

To run the java base cucumber test locally, the following additional step are
needed
- patch the pom.xml, par-config.properties and shared-driver.properties files

## Issues
- there are some hard coded paths in the pom.xml file
- Needs to sort out getting the browserVersion from the configuration file.
- only speed and need more (object.)waitForPageLoad(); function calls.

## Patch files

The major differences are in two patch files
- engine.patch has the changes necessary for the functionality to work with selenium 4.
- features.patch has all the other changes like adding waitForPageLoad().

### Generation
The patch files are generated using the following commands
``` shell
git diff tests/e2e/par-test-automation/src/main/java/uk/gov/beis/stepdefs/Hooks.java tests/e2e/par-test-automation/src/main/java/uk/gov/beis/supportfactory > .docksal/services/maven/engine.patch;
git diff tests/e2e/par-test-automation/src/test/resources > .docksal/services/maven/features.patch;
git diff tests/e2e/par-test-automation ':!tests/e2e/par-test-automation/src/main/java/uk/gov/beis/stepdefs/Hooks.java' ':!tests/e2e/par-test-automation/src/main/java/uk/gov/beis/supportfactory' ':!tests/e2e/par-test-automation/src/test/resources' > .docksal/services/maven/tests_code.patch;
```

This tracks the changes in the tests/e2e directory for the pom.xml file.
``` shell
git diff --no-index tests/e2e/par-test-automation/pom.xml .docksal/services/maven/pom.xml > .docksal/services/maven/maven-pom.patch
```

### Apply the patches
After tests/e2e directory is the same as in the repository, the patches can be applied

``` shell
git apply .docksal/services/maven/engine.patch;
git apply .docksal/services/maven/features.patch;
git apply .docksal/services/maven/tests_code.patch;
```

### Delete the old container so that rebuilding works

``` shell
docker container rm bpar2_maven_1
docker image rm bpar2_maven:latest
```

### Run the tests
Command to delete previous uploaded test file. link.txt
``` shell
echo "SELECT fid FROM file_managed WHERE filename LIKE '%ink%.txt';" | drush sqlc | xargs -I '{}' drush entity:delete file '{}'
```
`
Commands to claen up the database and run the tests
``` shell
fin drush --yes wd-del all;
fin drush --yes flood_unblock:all;
fin project restart maven;
```

### Extract the reports from the container
``` shell
rm -fr target;
docker export bpar2_maven_1 | tar -xvf - target
```
