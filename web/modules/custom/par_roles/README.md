# PAR Roles

This module is intended to manage the basic roles for all PAR Users.

Roles are managed by the `par_roles.role_manger` service.

## General vs Institutional

There are two types of roles:

- General roles are the default type assigned by another user on demand
- Institutional roles are automatically assigned based on the user's relationship to an institution (authority or organisation)

Institutional roles break down further for each type of institution, there are some roles that are only for authorities, and some that are only for organisations.

For example, the Processing Team Member (par_helpdesk) role can be assigned by other members of the Processing Team. Whereas the Authority Member (par_authority) role is assigned to any user who has joined an authority, unless they are assigned one of the other institution roles for an authority.

See `ParRoleManager::INSTITUTION_ROLES` & `ParRoleManager::INSTITUTION_ROLES` for the list of role types.

## Auto-assignment

Role management is automatic, the institution roles are auto assigned whenever a user account is saved.

A user **MUST** have one and only one institution role for each type of institution that they are a member of.

## Membership

A user's membership to an institution is part of the comlex network of relationships in Primary Authority

Whilst roles are automatically assigned, memberships are not, they must be managed individually. This means that when managing user roles and memberships...

**Memberships must be added or removed before roles are changed on the user account.**

Changing the role first may result in it being rejected.

### Role Rules

There are a handful of rules that **SHOULD** apply to the roles:

- If a user is a member of an institution they must have an institutional roles that corresponds to that institution type
- There should be at least one user in each institution
