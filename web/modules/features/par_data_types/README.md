# PAR Data Types
This feature is intended to define the different types of entities as set out by the PAR Data Model.

## What are the different types of entities?
The data types defined by the PAR Data Model include:
+ **Advice** - Details of any advice given to a Business or an Authority pertaining to a specific Partnership.
+ **Authority** - The Primary Authority is any authority that can provide businesses with robust and reliable regulatory advice.
+ **Business** - The business registered with PAR.
+ **Coordinatior** - A Coordinator is an intermediary association or franchise that can assist in the application to form a Partnership between a Business and an Authority.
+ **Inspection Plan** - Details of an inspection carried out against any given business.
+ **Partnership** - A partnership is the relationship between a Business and an Authority.
+ **Person** - A Person can be a member of any Primary Authority, Business or other legal institution that is responsible for an area of it's operation.
+ **Premises** - The premises that are owned by either a Business or Coordinator.
+ **Regulatory Area** - An area of regulation that can be applied to any Partnership between a Business and an Authority.

## What are the properties of these entities?
To be clear as to the data properties that we're trying to add with this feature see below:

### Business properties
+ **id** * = surrogate key
+ **phone** = single, string(255), free form, plain
+ **comments** = single, string (long), free form, html
+ **auth_premises** = single, boolean
+ **number_employees** * = single, string(255), limited choice (~5), plain
+ **sic_code** = multiple, int(6), free form
+ **company_type** = single, string(255), free form, plain
+ **name** = single, string(500), free form, plain
+ **email** = multiple, string(500), free form, plain
+ **business_type** = single, string(255), free form, plain
+ **nation** = single, string(255), limited choice (~5), plain
+ **first_name** = single, string(255), free form, plain
+ **last_name** = single, string(255), free form, plain
+ **trading_name** = single, string(255), free form, plain

### Authority properties
+ **id** * = surrogate key
+ **name** = single, string(500), free form, plain
+ **details** = single, string (long), free form, html
+ **nation** = single, string(255), limited choice (~5), plain
+ **authority_type** = single, string(255), free form, plain
+ **ons** = multiple, string(20), free form, plain

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

### Advice properties
+ **id** * = surrogate key
+ **advice_type** = single, string(20), limited choice (3), plain
+ **notes** = string (long), free form, html
+ **obsolete** = single, boolean

+ **visibile_authority** = single, boolean
+ **visibile_coordinator** = single, boolean
+ **visibile_business** = single, boolean

### Inspection Plan properties
+ **id** * = surrogate key
+ **valid_from** = single, date
+ **valid_to** = single, date

+ **approved_rd_exec** = single, boolean
+ **consulted_national_regulator** = single, boolean
+ **status** = single, boolean

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
