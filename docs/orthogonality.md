# Orthogonality

The Joomla! X architecture is based on the principle of orthogonal components.
This principle describes vertical components, like content, weblinks, or users,
that are combined with horizontal components, like workflow, tagging, or versioning.
Horizontal components (HCs) add functionality to vertical components (VCs) *without explicit code* in the components.

This document defines the communication between those two kinds of components.

## Scope

For each possible combination of installed HCs and VCs, the administrator can define,
whether or not this combination is supported. This could be represented by a list of supported HCs for each VC:

```json
{
    "content": [
        "workflow",
        "tagging",
        "versioning"
    ],
    "weblinks": [
        "workflow"
    ],
    "users": [
        "workflow",
        "tagging"
    ]
}
```

## Storage

Horizontal components must be able to add fields, relations, and conditions on read access,
and handle persistence of added information on write access.

Components use Entities to communicate with the storage layer, which are created by an EntityBuilder.
It is the responsibility of the EntityBuilder to provide additional fields, which are populated on read,
and to provide the necessary information for the Persistor to handle additional data correctly.

  - The `create()` method of the EntityBuilder needs to be modified to add the relation definitions
    of all associated HCs.

  - Since related data is stored as a child Entity in the main Entity,
    the Persistor can handle storage based on the information from the child Entity.

  - A HC must provide a condition (might be empty) for use in retrievals.
    The workflow HC, fx., should add `['state', Operator::EQ, Workflow::PUBLISHED]`,
    so that by default only published items are shown.

## Rendering

  - On list views, the HCs should provide filters for the fields, they add.
  
  - Content types based on entities (e.g., Article, Teaser) create a compound element from the entity's properties.
    A `ContentTypeQuery` is issued, and the `ContentTypeQueryHandler` is responsible to let the HCs add their data.
    *The current implementation contains just a dummy for that, just adding `extended => YES` to the elements.*
