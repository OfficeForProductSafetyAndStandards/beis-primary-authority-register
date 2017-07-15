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
+ **type** = single, string(255), limited choice (~5), plain, "An internally used sub-type, there can just be one sub-type if none are actually required."
+ **label** = single, string(255), free form, plain, "An internally used administration name, can be generated automatically where not needed"
+ **status** = single, boolean, "Whether published or archived"
+ **uid** = single, integer "The id of the user who created it"

## What are the fields of these entities?
To be clear as to the fields that we're trying to add with this feature see below:

**NOTE:** All field names are prepended with 'field_' in Drupal.

### Advice fields
+ **advice_type** = single, string(255), limited choice (~5), plain
+ **notes** = string (long), free form, html
+ **obsolete** = single, boolean
+ **visible_authority** = single, boolean
+ **visible_coordinator** = single, boolean
+ **visible_business** = single, boolean

### Inspection Plan properties
+ **valid_from** = single, _date_
+ **valid_to** = single, _date_
+ **approved_rd_exec** = single, boolean
+ **consulted_national_regulator** = single, boolean
+ **status** = single, string(255), limited choice (~5), plain

### Authority properties
+ **name** = single, string(500), free form, plain
+ **authority_type** = single, string(255), limited choice (3), plain
+ **details** = single, string (long), free form, html
+ **nation** = single, string(255), limited choice (~5), plain
+ **ons** = single, string(255), free form, plain

+ **person** = multiple, int(6), _reference_ to a Person
+ **premises** * = multiple, reference to Premises

### Organisation (Business) properties
+ **name** = single, string(500), free form, plain
+ **size** * = single, string(255), limited choice (~5), plain
+ **number_employees** * = single, string(255), limited choice (~5), plain
+ **nation** = single, string(255), limited choice (~5), plain
+ **comments** = single, string (long), free form, html
+ **premises_mapped** = single, boolean
+ **sic_code** = multiple, int(6), reference
+ **trading_name** = multiple, string(255), free form, plain

+ **person** = multiple, int(6), _reference_ to a Person
+ **premises** * = multiple, reference to Premises
+ **legal_entity** * = multiple, reference to Premises

### Organisation (Coordinator) properties
+ **name** = single, string(500), free form, plain
+ **size** * = single, string(255), limited choice (~5), plain
+ **employees_band** * = single, string(255), limited choice (~5), plain
+ **nation** = single, string(255), limited choice (~5), plain
+ **comments** = single, string (long), free form, html
+ **premises_mapped** = single, boolean
+ **sic_code** = multiple, int(6), reference
+ **trading_name** = multiple, string(255), free form, plain
+ **number_eligible** = single, int(6), free form

+ **person** = multiple, int(6), _reference_ to a Person
+ **premises** * = multiple, reference to Premises
+ **legal_entity** * = multiple, reference to Premises

### Partnership properties
+ **partnership_type** = single, string(255), limited choice (3), plain
+ **status** = single, string(255), limited choice (~5), plain
+ **about_partnership** = single, string (long), free form, html
+ **communication_email** * = single, boolean
+ **communication_phone** * = single, boolean
+ **communication_notes** * = single, string (long), free form, html
+ **approved_date** = single, _date_
+ **expertise_details** = single, string (long), free form, html
+ **cost_recovery** = single, string(255), limited choice (~5), plain
+ **reject_comment** = single, string (long), free form, html
+ **revocation_source** = single, string(255), limited choice (~5), plain
+ **revocation_date** = single, _date_
+ **revocation_reason** = single, string (long), free form, html
+ **authority_change_comment** = single, string (long), free form, html
+ **organisation_change_comment** = single, string (long), free form, html

+ **organisation** = multiple, _reference_ to an Organisation
+ **authority** = single, _reference_ to an Authority
+ **advice** = multiple, _reference_ to an Advice
+ **inspection_plan** = multiple, _reference_ to an Inspection Plan
+ **person** = multiple, int(6), _reference_ to a Person

### Premises properties
+ **address** * = single, _address_
  - address_line_1
  - address_line_2
  - post_code
  - locality (City)
  - administrative_area (County)
  - country

### Person properties
+ **title** = single, string(255), free form, plain
+ **name** * = single, string(500), free form, plain
+ **work_phone** = single, string(255), free form, plain
+ **mobile_phone** = single, string(255), free form, plain
+ **email** = single, string(500), free form, plain

### Legal Entity properties
+ **registered_name** = single, string(255), free form, plain
+ **registered_number** = single, int(10)
+ **legal_entity_type** = single, string(255), limited choice (~5), plain

### Regulatory Area properties
+ **name** * = single, string(500), free form, plain

### Enforcement Notice properties
+ **notice_type** = single, string(255), limited choice (~5), plain
+ **notice_date** = single, date

+ **primary_authority** = single, _reference_ to an Authority
+ **enforcing_authority** = single, _reference_ to an Authority
+ **legal_entity** * = single, reference to Premises


## What information do we need about fields to properly define them?
When adding data properties in Drupal there are certain things that affect how the data structure is stored and can't change after the field has data in it.
* Number of values allowed (one or many)
* Type of value stored (string, int, boolean, or compound types such as date, address, files or reference to another entity)
* Length of string and int values allowed
* Whether the value is a limited choice (lists, checkboxes) or free form entry (textfields)
* Expected contents of any string fields (html or plain)
* Any restrictions on file types
* Whether any two fields with multiple values should be grouped together. Typically with the provided data model if this is required they will be made their own entities, for example Premises.

We should take another look at the answers to these questions before we run the final migration to see if we can lock these down.

## Data property requirements
There are a number of naming conventions and requirements to meet for the GOV.UK design patterns and guidance assessments:
* [Names](https://www.gov.uk/service-manual/design/names)
* [Addresses](https://www.gov.uk/service-manual/design/addresses)
* [Dates](https://www.gov.uk/service-manual/design/dates)
* [Email addresses](https://www.gov.uk/service-manual/design/email-addresses)
* [Gender or sex](https://www.gov.uk/service-manual/design/gender-or-sex)
