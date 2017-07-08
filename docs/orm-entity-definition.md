# ORM: Entity Definition

For the Joomla ORM, entities are defined using XML files.
Each extension provides the descriptions of its entities and their relations.
The installer copies the definitions into the main repository, resolving the relations and adding the counter-relations.
 
## Entity

The root element is `entity`.

```xml
<entity name="..." role="default">
    <storage>...</storage>
    <fields>...</fields>
    <relations>...</relations>
</entity>
```

- `name` (required): The fully qualified class name of the represented entity,
                     like `<Vendor>\<Extension>\Entity\<Class>`.
- `role` (optional): The role of this entity, i.e., how it is used. Possible values are
    - `"primary"`: This is the main entity of the extension, the default aggregate root.
    - `"lookup"`: The associated data source is only used to lookup values. 
                It will not receive information about counter-relations. 
    - `"map"`: This entity connects two entities with a n:m relation.
    - `"inline"`: This entity has no own database table, but is serialised into a single field.
                It can be used to handle fx. parameters, which are stored JSON encoded in `params` of another entity.
    - `"default"`: This is the default role.
                 All relations are resolved and counter-relations added.
                 No special handling is applied.

### Storage

Storage defines how the entity is persisted.

#### Default

```xml
<storage>
    <default table="..."/>
</storage>
```

The default storage uses the persistence layer defined in the system, usually a RDBMS like MySQL.

- `table` (required): The name of the database table (without prefix).

#### CSV

```xml
<storage>
    <csv file="..."/>
</storage>
```

- `file` (required): The name of the data file.

#### API

```xml
<storage>
    <api base-url="..." handler="..."/>
</storage>
```

- `base-url` (optional): The base URL for the API.
- `handler` (required): The DataMapper class handling the communication with the API.

#### Special

```xml
<storage>
    <special dsn="..."/>
</storage>
```

- `dsn` (required): The DSN of the storage service.

### Fields

The `fields` section contains the definition of simple fields and fieldsets (compound fields).

```xml
<fields>
    <field>...</field>
    <field>...</field>
    <fieldset>...</fieldset>
    <fieldset>...</fieldset>
</fields>
```

#### Field

A `field` represents an entity property.

```xml
<field 
    name="..."
    type="..."
    size="..."
    input="..."
    default="..."
    filter="..."
    multiple="..."
    required="..."
    label="..."
    description="..."
    hint="...">
    <option>...</option>
    <option>...</option>
    <validation>...</validation>
    <validation>...</validation>
</field>
```

- `name` (required): The name of the field in underscore format. 
- `type` (required): The data type. Possible values are
    - `"string"`: The PHP string type.
    - `"integer"`: The PHP integer type.
    - `"float"`: The PHP float/double type.
    - `"boolean"`: The PHP boolean type.
    - `"date"`: A date.
    - `"datetime"`: A date with time.
    - `"foreignkey"`: A reference to another entity.
    - `"json"`: JSON encoded data.
    - `"virtual"`: This is a field without representation in the persistence layer.
                   It is used in forms only (think of the "retype your password" field).
- `size` (optional): The maximum size (length) of the data.
- `input` (optional): The identifier of an input control.
- `default` (optional): The default value.
- `filter` (optional): The sanitise filter.
- `readonly` (optional): If `"true"`, do not create an input control. Default is `"false"`.
- `multiple` (optional): If `"true"`, allow multiple (comma separated) values. Default is `"false"`.
- `required` (optional): If `"true"`, this field is required. Default is `"false"`.
- `label` (optional): The field label.
- `description` (optional): The field description.
- `hint` (optional): The field hint (placeholder).

##### Option

Some input controls allow to choose between different options.

```xml
<option value="...">...</option>
```

- `value` (optional): The value for the option. 
- `label` (required): The label (display value) for the option.

##### Validation

```xml
<validation rule="..." value="..."/>
```

- `rule` (required): The name of the validation rule.
- `value` (optional): The value for the rule. Defaults to `true`. 

#### Fieldset

A fieldset represents an entity that is stored directly in a serialised format.

##### Fieldset Syntax 1

The embedded entity can be referenced or defined inline:

```xml
<fieldset 
    name="..."
    type="..."
    entity="..."
    label="..."
    description="...">
    <field>...</field>
    <field>...</field>
</fieldset>
```

- `name` (required): The name of the field in underscore format. 
- `type` (required): The data type. Possible values are
    - `"json"`: JSON encoded data.
    - `"string"`: The PHP string type for other serialisation methods.
- `entity` (optional): The entity represented by this fieldset.
                       If omitted, a stdClass object with the fields defined in the fieldset will be used.
- `label` (optional): The fieldset label.
- `description` (optional): The fieldset description.

If the `field` elements are omitted, a separate definition of `<entity>` must exist.

##### Fieldset Syntax 2

The type of the embedded entity can depend on the value of another field.

```xml
<fieldset 
    name="..."
    type="..."
    label="..."
    description="...">
    <case field="..." value="..." use="..."/>
    <case field="..." value="..." use="..."/>
</fieldset>
```

- `name` (required): The name of the field in underscore format. 
- `type` (required): The data type. Possible values are
    - `"json"`: JSON encoded data.
    - `"string"`: The PHP string type for other serialisation methods.
- `label` (optional): The fieldset label.
- `description` (optional): The fieldset description.

For the `case` element:

- `field` (required): The field containing the switch value
- `value` (required): The value to match.
- `use` (required): The entity to be embedded, if the value matches.

### Relations

```xml
<relations>
    <belongsTo name="..." entity="..." reference="..."/>
    <belongsToMany name="..." entity="..." reference="..."/>
    <hasOne name="..." entity="..." reference="..."/>
    <hasMany name="..." entity="..." reference="..."/>
    <hasManyThrough name="..." entity="..." reference="..." joinTable="..." joinRef="..."/>
</relations>
```

#### belongsTo

```xml
<belongsTo name="..." entity="..." reference="..."/>
```

- `name`: The name of the (virtual) field in this entity
- `entity`: The type of the related entity
- `reference`: The field name in this entity pointing to the related entity
- `label` (optional): The label for the related entity.
- `description` (optional): The description for the related entity.

#### belongsToMany

```xml
<belongsToMany name="..." entity="..." reference="..."/>
```

- `name`: The name of the (virtual) field in this entity
- `entity`: The type of the related entities
- `reference`: The field name in this entity pointing to the related entities
- `label` (optional): The label for the related entities.
- `description` (optional): The description for the related entities.

#### hasOne

```xml
<hasOne name="..." entity="..." reference="..."/>
```

- `name`: The name of the (virtual) field in this entity
- `entity`: The type of the related entity
- `reference`: The field name in the related entity pointing to this entity
- `label` (optional): The label for the related entity.
- `description` (optional): The description for the related entity.

#### hasMany

```xml
<hasMany name="..." entity="..." reference="..."/>
```

- `name`: The name of the (virtual) field in this entity
- `entity`: The type of the related entities
- `reference`: The field name in the related entities pointing to this entity
- `label` (optional): The label for the related entities.
- `description` (optional): The description for the related entities.

#### hasManyThrough

```xml
<hasManyThrough name="..." entity="..." reference="..." joinTable="..." joinRef="..."/>
```

- `name`: The name of the (virtual) field in this entity
- `entity`: The type of the related entities
- `reference`: The field name in the map pointing to this entity
- `joinTable`: The map containing pointers to both related entities
- `joinRef`: The field name in the map pointing to the related entities
- `label` (optional): The label for the related entities.
- `description` (optional): The description for the related entities.

## ToDo

- [ ] Implement handling of entity role
- [ ] Implement fieldset handling
- [ ] Implement belongsToMany handling
- [ ] Move Entity DTD to a Joomla repository when it is stable enough
- [ ] Replace DTD with XMLSchema, so `<xs:alternative test="@type=string">` can be used to specify attributes
      that are specific to certain field types. See [this StackOverflow answer](https://stackoverflow.com/questions/27878402/how-to-make-type-depend-on-attribute-value-using-conditional-type-assignment#answer-27880329) for more information.

## Resources

- [Entity DTD](https://github.com/nibralab/joomla-architecture/blob/master/code/Joomla/ORM/Definition/entity.dtd)

