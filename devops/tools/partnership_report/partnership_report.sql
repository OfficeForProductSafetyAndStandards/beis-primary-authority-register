COPY(
SELECT
    par_partnerships_field_data.approved_date,
    par_partnerships_field_data.revocation_date,
    par_partnerships_field_data.partnership_status,
    par_partnerships_field_data.partnership_type,
    par_authorities_field_data.authority_name,
    par_organisations_field_data.organisation_name,
    par_legal_entities_field_data.registered_name,
    par_inspection_plans_field_data.inspection_status,
    par_advice_field_data.advice_type,
    par_sic_codes_field_data.description,
    par_organisations_field_data.employees_band,
    par_authorities_field_data.nation,
    par_advice_field_data.deleted,
    par_advice_field_data.revoked,
    par_inspection_plans_field_data.deleted,
    par_inspection_plans_field_data.revoked,
    par_legal_entities_field_data.deleted,
    par_legal_entities_field_data.revoked,
    par_data_organisation__trading_name.trading_name_value
FROM
    par_partnerships_field_data
        LEFT OUTER JOIN par_data_partnership__field_advice ON (par_partnerships_field_data.id = par_data_partnership__field_advice.entity_id)
            LEFT OUTER JOIN par_advice_field_data ON (par_data_partnership__field_advice.field_advice_target_id = par_advice_field_data.id)
        LEFT OUTER JOIN par_data_partnership__field_authority ON (par_partnerships_field_data.id = par_data_partnership__field_authority.entity_id)
            LEFT OUTER JOIN par_authorities_field_data ON (par_data_partnership__field_authority.field_authority_target_id = par_authorities_field_data.id)
        LEFT OUTER JOIN par_data_partnership__field_organisation ON (par_partnerships_field_data.id = par_data_partnership__field_organisation.entity_id)
        LEFT OUTER JOIN par_data_partnership__field_inspection_plan ON (par_partnerships_field_data.id = par_data_partnership__field_inspection_plan.entity_id)
            LEFT OUTER JOIN par_inspection_plans_field_data ON (par_data_partnership__field_inspection_plan.field_inspection_plan_target_id = par_inspection_plans_field_data.id),
    par_organisations_field_data
        LEFT OUTER JOIN par_data_organisation__trading_name ON (par_organisations_field_data.id = par_data_organisation__trading_name.entity_id)
        LEFT OUTER JOIN par_data_organisation__field_legal_entity ON (par_organisations_field_data.id = par_data_organisation__field_legal_entity.entity_id)
            LEFT OUTER JOIN par_legal_entities_field_data ON (par_data_organisation__field_legal_entity.field_legal_entity_target_id = par_legal_entities_field_data.id)
        LEFT OUTER JOIN par_data_organisation__field_sic_code ON (par_organisations_field_data.id = par_data_organisation__field_sic_code.entity_id)
            LEFT OUTER JOIN par_sic_codes_field_data ON (par_data_organisation__field_sic_code.field_sic_code_target_id = par_sic_codes_field_data.id)
WHERE
    par_data_partnership__field_organisation.field_organisation_target_id = par_organisations_field_data.id
AND 
    (par_partnerships_field_data.revoked is null OR par_partnerships_field_data.revoked = 0)
AND 
    (par_partnerships_field_data.deleted is null OR par_partnerships_field_data.deleted = 0)
AND 
    (par_authorities_field_data.revoked is null OR par_authorities_field_data.revoked = 0)
AND 
    (par_authorities_field_data.deleted is null OR par_authorities_field_data.deleted = 0)
AND 
    (par_organisations_field_data.revoked is null OR par_organisations_field_data.revoked = 0) 
AND 
    (par_organisations_field_data.deleted is null OR par_organisations_field_data.deleted = 0)            
AND 
    NOT par_partnerships_field_data.partnership_status = 'n/a'
AND
    par_partnerships_field_data.status = 1
ORDER BY
    par_authorities_field_data.authority_name,
    par_organisations_field_data.organisation_name,
    par_legal_entities_field_data.registered_name,
    par_advice_field_data.advice_type
) To stdout With CSV DELIMITER ',';

