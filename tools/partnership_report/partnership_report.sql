COPY(
    SELECT
    par_partnerships_field_data.approved_date,
    par_partnerships_field_data.revocation_date,
    par_partnerships_field_data.partnership_status,
    par_partnerships_field_data.partnership_type,
    par_authorities_field_data.authority_name,
    par_organisations_field_data.organisation_name,
    par_legal_entities_field_data.registered_name,
    1,
    par_inspection_plans_field_data.inspection_status,
    par_advice_field_data.status,
    par_sic_codes_field_data.sic_code,
    par_organisations_field_data.employees_band,
FROM
    par_partnerships_field_data,
    par_data_partnership__field_authority,
    par_authorities_field_data,
    par_data_partnership__field_organisation,
    par_organisations_field_data,
    par_data_organisation__field_legal_entity,
    par_legal_entities_field_data,
    par_data_partnership__field_inspection_plan,
    par_inspection_plans_field_data,
    par_data_partnership__field_advice,
    par_advice_field_data,
    par_data_organisation__field_sic_code,
    par_sic_codes_field_data
WHERE
    par_partnerships_field_data.id = par_data_partnership__field_authority.entity_id
AND
    par_partnerships_field_data.id = par_data_partnership__field_organisation.entity_id
AND
    par_data_partnership__field_authority.field_authority_target_id = par_authorities_field_data.id
AND
    par_data_partnership__field_organisation.field_organisation_target_id = par_organisations_field_data.id
AND
    par_organisations_field_data.id = par_data_organisation__field_legal_entity.entity_id
AND
    par_data_organisation__field_legal_entity.field_legal_entity_target_id = par_legal_entities_field_data.id
AND
    par_partnerships_field_data.id = par_data_partnership__field_inspection_plan.entity_id
AND
    par_data_partnership__field_inspection_plan.field_inspection_plan_target_id = par_inspection_plans_field_data.id
AND
    par_partnerships_field_data.id = par_data_partnership__field_advice.entity_id
AND
    par_data_partnership__field_advice.field_advice_target_id = par_advice_field_data.id
AND
    par_organisations_field_data.id = par_data_organisation__field_sic_code.entity_id
AND
    par_data_organisation__field_sic_code.field_sic_code_target_id = par_sic_codes_field_data.id    
AND 
    par_partnerships_field_data.revoked is null AND par_partnerships_field_data.deleted is null
AND 
    par_authorities_field_data.revoked is null AND par_authorities_field_data.deleted is null
AND 
    par_legal_entities_field_data.revoked is null AND par_legal_entities_field_data.deleted is null    
AND 
    par_organisations_field_data.revoked is null AND par_organisations_field_data.deleted is null            
AND 
    par_inspection_plans_field_data.revoked is null AND par_inspection_plans_field_data.deleted is null    
AND 
    par_advice_field_data.revoked is null AND par_advice_field_data.deleted is null
ORDER BY
    par_authorities_field_data.authority_name,
    par_organisations_field_data.organisation_name,
    par_legal_entities_field_data.registered_name
) To stdout With CSV DELIMITER ',';

