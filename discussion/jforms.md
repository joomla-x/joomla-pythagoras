# Data Definitions (JForms)

## Decision

JForm will be refactored to support not only form generation, but also list and detail views. The 
input is provided by a form definition class, which is populated through an XML file, or ­ if 
feasible ­ through JSON or Yaml files (optionally). 

## Reason

JForm provides a template independent gateway to data structures. Supporting list and detail 
views in addition to the forms will reveal the real power of the concept. Allowing other file 
formats for the definition input will enhance flexibility. 

## Discussion

*This is a collection of statements and comments on Glip regarding general development questions.*

@2015-09-02 20:50 UTC
