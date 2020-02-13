# PAR Data Model Entities
This module is intended to create the PAR Data entity types required as set out by the PAR Data Model.

### What should it do?
This module should define the base entity definitions for each entity in the Physical model as well as the validation or configuration for the PAR Data.

### What should it not do?
This module should not encapsulate any business logic into the retrieval of data, this should be handled be a separate module. This module should also not require any dependencies on any other PAR modules.

### Why are we using custom entities?
We have decided to use custom entities because the schema and relationship requirements for some of the entities are likely to vary from standard pages.

There is also likely to be non-standard properties, workflows and/or indexes, and permissions on these entities. So as a full separation of concerns we are using custom entities instead of nodes.

### Why are we storing configuration on the Entity Type?
There is enough flexibility in the data model that at the point of creation it could not be determined whether any of the given fields or properties should have any validation, restrictions or requirments set upong them. And therefore using any other field types than basic string and text fields would have imposed unnecessary restrictions on the field types.

## What are the different types of Entities?
The data types defined by the PAR Data Model include:
+ **Advice** - Advice is given by a Primary Authority in the context of a Partnership. There are three known sub-types of Advice: To LA, To Business, Background Information.
+ **Inspection Plan** - An Inspection Plan is a template for carrying out particular types of inspections that has been agreed with the Primary Authority in a partnership; all enforcement officers from all local authorities have to use that plan.
+ **Authority** - An Authority is a government body, usually a local authority but occasionally a fire authority or port authority.
+ **Organisation (Business)** - A Business is an Organisation - usually a commercial one, but not always - that is covered (or intends to be covered) by a Primary Authority Partnership. The latter may be indirect ("co-ordinated") or direct.
+ **Organisation (Coordinator)** - A Co-ordinator is generally a trade association or a franchise group who have a Primary Authority Partnership on behalf of, or for the benefit of, their members or franchisees.
+ **Partnership** - A Partnership is a relationship between a Primary Authority and either a Business ("direct partnership") or a Co-ordinator ("co-ordinated partnership").  Note that in the latter case, the Business records may or may not be held in the PAR3 database.
+ **Person** - A Person is a named individual who can feature in a number of different ways within PAR.  A Person may, or may not, be a user of the PAR application..
+ **Premises** - Premises are a location used by either an Authority or an Organisation.
+ **Regulatory Function** - PAR3 will cover 7 high-level Regulatory Areas, namely: Environmental Health, Trading Standards, Fire Safety, Licensing, Petrol Storage Certification, Explosives Licensing, Health and Safety (Scotland).
+ **Legal Entity** - A Legal Entity is a representation of an Organisation via some formal method of registration or else a less formal declaration, there are currently three types of Legal Entity: Registered Charity, Limited Company, Sole Trader.
+ **SIC Code** - An area of regulation that can be applied to any Partnership between a Business and an Authority.
+ **Enforcement Notice** - An Enforcement Notice is a legal document that contains one or more Enforcement Actions. It is initiated by an Enforcement Officer working for an Authority ("the Enforcing Authority"). It will be made against one (and only one) Legal Entity.
+ **Enforcement Action** - An Enforcement Action is relates to a specific task to be carried out on a notice. It specifically deals with one Regulatory Function and one Legal Entity.

## Data property requirements
There are a number of naming conventions and requirements to meet for the GOV.UK design patterns and guidance assessments:
* [Names](https://www.gov.uk/service-manual/design/names)
* [Addresses](https://www.gov.uk/service-manual/design/addresses)
* [Dates](https://www.gov.uk/service-manual/design/dates)
* [Email addresses](https://www.gov.uk/service-manual/design/email-addresses)
* [Gender or sex](https://www.gov.uk/service-manual/design/gender-or-sex)
