# PAR Flow 
This module allows the creation of form component plugins which are reusable sections of forms (or any render array for that matter).

It is not compulsory to use these plugins in any forms, but if there is likely to be any reuse it is highly recommended.

Re-using form elements this way reduces the dependencies on other modules and provides a few nice little extras as mentioned below.

## Plugins
Plugins reuse the form elements, but also the logic of how to load data into these elements and how to validate them.

They also support the ability to render and validate multiple occurrences of these components. Pass `cardinality` as an integer or `-1` for unlimited values.

## Adding a plugin
To add a plugin to a given form step:
```yaml
1:
  route: module_name.route_name
  form_id: unique_form_id
  components:
    plugin_name:
      cardinality: -1
```
