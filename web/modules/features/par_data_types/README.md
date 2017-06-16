# PAR Data Types
This feature is intended to define the different types of entities as set out by the PAR Data Model.

## What are the different types of entities?
The data types defined by the PAR Data Model include:
* Advice - Details of any advice given to a Business or an Authority pertaining to a specific Partnership.
* Authority - The Primary Authority is any authority that can provide businesses with robust and reliable regulatory advice.
* Business - The business registered with PAR.
* Coordinatior - A Coordinator is an intermediary association or franchise that can assist in the application to form a Partnership between a Business and an Authority.
* Inspection Plan - Details of an inspection carried out against any given business.
* Partnership - A partnership is the relationship between a Business and an Authority.
* Person - A Person can be a member of any Primary Authority, Business or other legal institution that is responsible for an area of it's operation.
* Premises - The premises that are owned by either a Business or Coordinator.
* Regulatory Area - An area of regulation that can be applied to any Partnership between a Business and an Authority.

## What are the properties of these entities?
### Business

business_id = primary_id
phone = phone (e.g. "07884 068024")
comments = long plain text
auth_premises = boolean (e.g. "N")
employees_band = list range (e.g. "10-49")
sic_code = int (6)
company_type = short plain text
name = short plain text
email = email
business_type = list plain text (e.g. "Short")
nation = list plain text (e.g. "England")
bv3_id = int
first_name = short plain text
surname = short plain text
trading_name = short plain text

"50"|"07884 068024"|""|"N"|"10-49"|""|""|"Balsab Limited T/A KFC"|"balsabltd@gmail.com"|"Small"|"England"|""|"Tejpal"|"Singh"|"Balsab Limited KFC Kentucy Fried Chicken"
"53"|"07909 962 363"|""|"N"|"250+"|""|""|"Castlebarn Limited T/A KFC"|"dcatterall@gmail.com"|"Small"|"England"|""|"David"|"Catterall"|"Castlebarn Limited KFC Kentucy Fried Chicken"^M
"58"|"07970 618 322"|""|"N"|"50-249"|""|""|"NNA Limited T/A KFC"|"martina@gmnltd.co.uk"|"Small"|"England"|""|"Martina"|"Nicholas"|"NNA Limited KFC Kentucy Fried Chicken"^M
"69"|"07747 792 059"|""|"N"|"50-249"|""|""|"K&Z Enterprises Limited T/A KFC"|"shahaz1@yahoo.com"|"Small"|"England"|""|"Shahaz"|"Nanji"|"K&Z Enterprises, K&Z Wallington, K&Z Newhaven KFC Kentucy Fried Chicken"^M
"74"|"01494 524775"|""|"N"|"50-249"|""|""|"PLV Enterprises T/A KFC"|"peter.v@plventerprises.co.uk"|"Small"|"England"|""|"Peter"|"Vujasevic"|"PLV Enterprises KFC Kentucy Fried Chicken"^M
"94"|"01788 551111"|""|"N"|"0-9"|""|""|"Sweet Let Limited"|"matthew@brownandcockerill.co.uk"|"Small"|"England"|""|"Matthew"|"Sweet"|"Brown & Cockerill Lettings"