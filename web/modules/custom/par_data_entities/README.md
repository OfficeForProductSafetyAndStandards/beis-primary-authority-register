# PAR Data Model Entities
This module is intended to create the base for all PAR entities required as set out by the PAR Data Model.

### What should it do?
This module should define the base entity definitions.

### What should it not do?
This module should not contain any entities, these should reside in a separate feature module.

### Why are we using custom entities?
We have decided to use custom entities because the schema and relationship requirements for some of the entities are likely to vary from standard pages.

There is also likely to be non-standard properties, workflows and/or indexes on these entities. So as a full separation of concerns we are using custom entities instead of nodes.