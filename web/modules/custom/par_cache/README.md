# PAR Caching

This modules creates some persistent cache bins that won't be cleared when flushing drupal caches. This enables deployments to be scheduled without clearing user data defined caches.

## Cache bins
* **par_data** - for storing relationships between par_data entities, such that relationship trees can be easily referenced.
* **par_flows** - for storing multistep form caches and user temporary form data between sessions.

**Note:** Other modules may rely on the existence of these bing, but theses modules cannot declare dependencies on this module due to the nature of cache backend instantiation within Drupal. If this modules is disabled it may disrupt cache behaviour of the above functions.

## Clearing persistent caches
The following drush commands are available to clear the persistent cache backends:
* drush par-cache:clear _{bin}_

It is up-to developers to clear these bins if there are changes to the structure of the stored cache objects that would require a rebuild.
