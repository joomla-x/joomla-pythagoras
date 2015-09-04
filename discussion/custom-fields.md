# Custom Fields

## Decision

Custom fields will get implemented as a horizontal component. 

## Reason

As a horizontal component, custom fields are provided for any existing and future vertical 
component. 

## Discussion

*This is a collection of statements and comments on Glip regarding custom fields.*

George Wilson
> caveat I haven't looked at the code at all. But
> [Is there a need for basic custom fields in Joomla core?](https://groups.google.com/forum/#!topic/joomla-dev-general/rsZsQgpZToU)
> might be worth looking at for the custom fields in J4

Niels Braczek
> Seems to be the right approach (modifying JForm on the fly)

George Wilson
> Exactly. As I say I've never used this or looked at code. But based on their description it sounds like it does the right things

Niels Braczek
> I had a short look at the code. It's of course not orthogonal yet, but it can serve as a good starting point.

Nicholas Dionysopoulos
> Yup. That's pretty much what I had in mind.

@2015-09-04 03:50 UTC
