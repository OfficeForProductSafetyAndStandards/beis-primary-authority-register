COPY(
    SELECT
    par_partnerships_field_data.approved_date,
    par_partnerships_field_data.revocation_date,
    par_partnerships_field_data.partnership_status,
    par_partnerships_field_data.partnership_type,
    par_authorities_field_data.authority_name,
    par_organisations_field_data.organisation_name
FROM
    par_partnerships_field_data,
    par_data_partnership__field_authority,
    par_authorities_field_data,
    par_data_partnership__field_organisation,
    par_organisations_field_data
WHERE
    par_partnerships_field_data.id = par_data_partnership__field_authority.entity_id
AND
   par_partnerships_field_data.id = par_data_partnership__field_organisation.entity_id
AND
   par_data_partnership__field_authority.field_authority_target_id = par_authorities_field_data.id
AND
   par_data_partnership__field_organisation.field_organisation_target_id = par_organisations_field_data.id
) To stdout With CSV DELIMITER ',';

