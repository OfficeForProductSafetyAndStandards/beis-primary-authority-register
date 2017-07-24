# PAR Flow 
This module is intended to create the flow that enables transitions between pages, whilst storing any data held in forms.

This module also provides base controllers for both Forms and Pages, to allow both to be added to a flow and inherrit many of the transition and data storage/retrieval functions that are necessary for Flows.

## Overview
The central concept of a Flow is based around a Flow Entity, this defines the steps that will be followed in any given journey and provides a default order with which these will be followed.

A basic flow entity would look like this:

```
  id: example
  label: Partnership Application
  description: 'An example flow.'
  steps:
    1:
      route: 'par_demo_forms.overview'
      form_id: 'par_demo_overview'
    2:
      route: 'par_demo_forms.first'
      form_id: 'par_demo_first'
    3:
      route: 'par_demo_forms.second'
```

As can be seen forms should specify their form_id, which will be used as part of the key to save and retrieve any data they wish to set.

The data they set is stored in the Private Temp Store and can be retrieved only by another Form in this Flow. Pages are not able to access this data.

## Base Classes
The routes defined in a Flow must implement either the ParBaseForm or ParBaseController abstract classes, or they will not be able to access the transitions or store/retrieve data.

* Data storage/retrieval methods are proteted and as such only documented in the ParBaseForm method.
* Flow transition methods are all public and are all documented on the ParFlowInterface. There are additional transition methods available as part of the ParRedirectTrait which can be used by other classes.
