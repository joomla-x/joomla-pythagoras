# Routing

## Decision

The routers along the lines suggested by Niels and Hannes (with possible additions from the 
Framework’s router) are used as a base. The menu system is used to generate custom route 
patterns for specific sites. The route must still run through JComponentHelper.  For REST we 
can support “best practice” URLs using the same router. 

## Reason

The approach provides ability for auto­generating route patterns, but allow for customization. 
Best practice RESTful URLs come out of the box. A router.php will no longer be needed in 
components. 
 
As there are going to be multiple MVC systems used within 3rd party components the routing 
must be independent of the MVC layer. JComponentHelper is therefore used to “dispatch” 
components in the CMS and therefore all routing should call the component helper. 

## References

  - [Router PoC](https://github.com/nibralab/joomla­architecture/blob/master/poc/router.php) 
  - [Hannes' router](https://github.com/Hackwar/joomla­cms/tree/jrouter) 
  - [Framework router](https://github.com/joomla­framework/router) 

## Discussion

*This is a collection of statements and comments on Glip regarding the router.*

@2015-09-04 03:50 UTC
