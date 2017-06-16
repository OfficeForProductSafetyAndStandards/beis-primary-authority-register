# PAR Data Model
This module is intended to be the base for all PAR entities required as set out by the PAR Data Model.

### Why are we using custom entities?
We have decided to use custom entities because the schema and relationship requirements for some of the entities are likely to vary from standard pages.

There is also likely to be non-standard properties, workflows and/or indexes on these entities. So as a full separation of concerns we are using custom entities instead of nodes.