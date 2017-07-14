# PAR Data Types
This feature is intended to define the different Entity Bundles as set out by the PAR Data Model.

## What are the different types of Entity Bundles?
The data types defined by the PAR Data Model include:
+ **Advice** - Advice is given by a Primary Authority in the context of a Partnership. There are three known sub-types of Advice: To LA, To Business, Background Information.
+ **Inspection Plan** - An Inspection Plan is a template for carrying out particular types of inspections that has been agreed with the Primary Authority in a partnership; all enforcement officers from all local authorities have to use that plan.
+ **Authority** - An Authority is a government body, usually a local authority but occasionally a fire authority or port authority.
+ **Business** - A Business is an Organisation - usually a commercial one, but not always - that is covered (or intends to be covered) by a Primary Authority Partnership. The latter may be indirect ("co-ordinated") or direct.
+ **Coordinator** - A Co-ordinator is generally a trade association or a franchise group who have a Primary Authority Partnership on behalf of, or for the benefit of, their members or franchisees.
+ **Partnership** - A Partnership is a relationship between a Primary Authority and either a Business ("direct partnership") or a Co-ordinator ("co-ordinated partnership").  Note that in the latter case, the Business records may or may not be held in the PAR3 database.
+ **Person** - A Person is a named individual who can feature in a number of different ways within PAR.  A Person may, or may not, be a user of the PAR application..
+ **Premises** - Premises are a location used by either an Authority or an Organisation.
+ **Regulatory Area** - PAR3 will cover 7 high-level Regulatory Areas, namely: Environmental Health, Trading Standards, Fire Safety, Licensing, Petrol Storage Certification, Explosives Licensing, Health and Safety (Scotland).
+ **Legal Entity** - A Legal Entity is a representation of an Organisation via some formal method of registration or else a less formal declaration, there are currently three types of Legal Entity: Registered Charity, Limited Company, Sole Trader.
+ **SIC Code** - An area of regulation that can be applied to any Partnership between a Business and an Authority.
+ **Enforcement Notice** - An Enforcement Notice is a legal document that contains one or more Enforcement Actions. It is initiated by an Enforcement Officer working for an Authority ("the Enforcing Authority"). It will be made against one (and only one) Legal Entity.

## There are some uniform properties that apply to all of these Entity Bundles
+ **id** = surrogate key, "An internal ID"
+ **uuid** = single, string, "A universally unique ID"
+ **label** = single, string(255), free form, plain, "An internally used administration name, can be generated automatically where not needed"
+ **status** = single, boolean, "Whether published or archived"
+ **uid** = single, integer "The id of the user who created it"

## What are the fields of these entities?
To be clear as to the fields that we're trying to add with this feature see below:

### Advice properties
+ **type** = single, string(255), limited choice (3), plain
+ **notes** = string (long), free form, html
+ **obsolete** = single, boolean
+ **visible_authority** = single, boolean
+ **visible_coordinator** = single, boolean
+ **visible_business** = single, boolean

### Inspection Plan properties
+ **valid_from** = single, date
+ **valid_to** = single, date
+ **approved_rd_exec** = single, boolean
+ **consulted_national_regulator** = single, boolean
+ **state** = single, string(255), limited choice (~5), plain

### Authority properties
+ **name** = single, string(500), free form, plain
+ **type** = single, string(255), limited choice (3), plain
+ **details** = single, string (long), free form, html
+ **nation** = single, string(255), limited choice (~5), plain
+ **ons** = multiple, string(20), free form, plain

### Business properties
+ **name** = single, string(500), free form, plain
+ **size** * = single, string(255), limited choice (~5), plain
+ **number_employees** * = single, string(255), limited choice (~5), plain
+ **nation** = single, string(255), limited choice (~5), plain
+ **comments** = single, string (long), free form, html
+ **premises_mapped** = single, boolean
+ **person** = single, int(6), reference
+ **sic_code** = multiple, int(6), reference
+ **trading_name** = multiple, string(255), free form, plain

### Coordinator properties
+ **name** = single, string(500), free form, plain
+ **type** = single, string(255), limited choice (3), plain
+ **size** * = single, string(255), limited choice (~5), plain
+ **number_employees** * = single, string(255), limited choice (~5), plain
+ **nation** = single, string(255), limited choice (~5), plain
+ **comments** = single, string (long), free form, html
+ **premises_mapped** = single, boolean
+ **person** = single, int(6), reference
+ **sic_code** = multiple, int(6), reference
+ **trading_name** = multiple, string(255), free form, plain
+ **number_eligible** = single, int(6), free form






### Coordinator properties
+ **id** * = surrogate key
+ **name** = single, string(500), free form, plain
+ **number_eligible** = int(6), free form
+ **email** = multiple, string(500), free form, plain
+ **coordinator_type** = single, string(255), free form, plain
+ **auth_businesses** = single, boolean
+ **nature_of_organisation** = single, string(255), free form, plain
+ **sic_code** = multiple, int(6), free form

### Partnership properties
+ **id** * = surrogate key
+ **business** = reference to Business
+ **primary_authority** = reference to Authority
+ **inspection_plan** * = multiple, file, pdf/docx
+ **about_partnership** = single, string (long), free form, html
+ **communication_post** * = single, boolean
+ **approved_date** = single, date
+ **communication_email** * = single, boolean
+ **expertise** = single, string (long), free form, html
+ **about_business** = single, string (long), free form, html
+ **cost_recovery** = single, string(20), limited choice (~5), plain
+ **revocation_source** = single, string(20), limited choice (~5), plain
+ **communication_other** * = single, string (long), free form, html
+ **business_certificate** * = multiple, file, pdf/docx
+ **primary_authority_certificate** * = multiple, file, pdf/docx
+ **partnership_categories** = multiple, string(255), limited choice (100+), plain
+ **la_change_comment** = single, string (long), free form, html
+ **revocation_reason** = single, string (long), free form, html
+ **business_change_comment** = single, string (long), free form, html
+ **partnership_type** = single, string(255), free form, plain
+ **partnership_sub_type** = single, string(255), free form, plain 
+ **reject_comment** = single, string (long), free form, html
+ **relation_categories** = multiple, string(255), limited choice (100+), plain

### Premises properties
+ **id** * = surrogate key
+ **business** * = reference to Business
+ **address** * = single, address
+ **nation** = single, string(255), limited choice (~5), plain
+ **uprn** = single, string(30), free form, plain

### Person properties
+ **id** * = surrogate key
+ **primary_authority** = reference to Authority
+ **coordinator** = reference to Coordinator
+ **title** = single, string(255), free form, plain
+ **first_name** = single, string(255), free form, plain
+ **last_name** * = single, string(255), free form, plain
+ **work_phone** = single, string(255), free form, plain
+ **mobile_phone** = single, string(255), free form, plain
+ **email** = multiple, string(500), free form, plain

### Legal Entity properties
+ **id** * = surrogate key
+ **business** = reference to Business
+ **registered_name** = single, string(255), free form, plain
+ **entity_number** = single, int(10)
+ **entity_type** = single, string(20), limited choice (~5), plain

### Regulatory Area properties
+ **id** * = surrogate key
+ **name** * = surrogate key

+ **visibile_authority** = single, boolean
+ **visibile_coordinator** = single, boolean
+ **visibile_business** = single, boolean

### Enforcement Notice properties
+ **id** * = surrogate key
+ **notice_type** = single, string(20), limited choice (~5), plain
+ **notice_date** = single, date

* ENFORCEMENT_NOTICE_ID NUMBER (11) F
* ENFORCING_AUTHORITY_ID NUMBER (11) F
* PRIMARY_AUTHORITY_ID NUMBER (11) F
* LEGAL_ENTITY_ID NUMBER (11)

 `*` Indicates properties that have been altered from the Alpha Data Model.

## What information do we need about properties to properly define them?
When adding data properties in Drupal there are certain things that affect how the data structure is stored and can't change after the property is created.
* Number of values allowed (one or many)
* Type of value stored (string, int, boolean, date or specific drupal type of address, file or reference to another entity)
* Length of string and int values allowed
* Whether the value is a limited choice (lists, checkboxes) or free form entry (textfields)
* Expected contents of any string fields (html or plain)
* Any restrictions on file types
* Whether any two fields with multiple values should be grouped together. As in, must have the same number of values, and value 2 of one field correspondends to value 2 of the other field

How these properties are stored affects the migration, the code we right, and how the site is configured. Therefore we need to get these things figured out asap.

The provisional answers to these questions have been indicated above for these properties separated by commas.

## Data property requirements
There are a number of naming conventions and requirements to meet for the GOV.UK design patterns and guidance assessments:
* [Names](https://www.gov.uk/service-manual/design/names)
* [Addresses](https://www.gov.uk/service-manual/design/addresses)
* [Dates](https://www.gov.uk/service-manual/design/dates)
* [Email addresses](https://www.gov.uk/service-manual/design/email-addresses)
* [Gender or sex](https://www.gov.uk/service-manual/design/gender-or-sex)
