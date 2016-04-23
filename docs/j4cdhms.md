# CDH Milestone

Show JoomlaX with basic **article** component [1] to serve as the proof of concept 
for the JoomlaX architecture. This proof of concept is meant as an example for education,
to show the workings and to discuss.

The concept will cover 

* Universal content types
* Rendering
* Storage
* Workflow
* Command bus

![Alt text](j4cdhms.png)

## [2] Universal content types ( UCT )
Abstract definitions of content. The majority of UCT's will be defined by the UX working group. 
For milestone purposes 2 to 4 UCT's will be defined by ARCH to show the working of the renderer.  

## [3] Renderer
Render the Content tree ( CT ) with a limited number of UCT's `

Plain HTML, without JS. The plain HTML renderer can serve as a base for
people with more JS skills.

## [4] Storage 
### CSV based storage
Outset for experimenting
### "ORM" like storage
System glue to storage layer. doctrine dbal
 
## [5] Workflow

* Published
* UnPublished
* Archived
* Trashed

The workflow will be implemented as an example of a horizontal component. 
The article component as vertical will "get" the horizontals functionality automatically

## [6] Channel independancy border

Show
* API
* Joomla PHP

## Command bus

tactician ( or something else as we decide to use librairis )
????

#NOTE

Term like ORM are the best aproximation of inteded functionality, in this example ORM does not mean a full fledged ORM as we might know it.
