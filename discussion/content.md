# Universal Content Model

## Decision

Content uses the Composite Pattern. There is no difference (internally) between categories and 
content items. *Any* component producing output implements the same Content Interface. 
Output is created using channel specific Renderers. 
According to Herman, PHPCR implements that concept already. 
Performance of pagination, edit, delete with PHPCR has to compared the same with current 
Joomla 3.x. 

## Reason

Content should be handled as content, not just articles. In reality, content is made up as a 
non­cyclic graph: paragraphs, images, or other content types can have multiple parents, so 
content gets re­usable. For each node, the site owner can choose to render its children as a list 
(like categories do now), or to compose a page from the children. 
Performance: Pagination, edit, delete, are hurting performance­wise in J3 currently. If PHPCR 
does not worsen things, it is the way to go. 

## References

  - [Content structure](http://nibralab.github.io/joomla­architecture/content­structure.html) 
  - [PoC for Renderer](https://github.com/nibralab/joomla­architecture/blob/master/poc/dynamic­renderer.php) 

## Discussion

*This is a collection of statements and comments on Glip regarding the Universal Content Model.*

@2015-09-04 03:50 UTC
