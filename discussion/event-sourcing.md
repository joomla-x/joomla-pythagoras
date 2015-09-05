# Event Sourcing

## Decision

Event Sourcing was deemed of interest to the attendees. The library “broadway” was proposed 
as a potential solution although it would need a good amount of integration with the 
CMS/Framework (the eventdispatcher, use of DBAL etc.). 
We need performance tests on this. 
Complexity for extension developers must be taken into account 

###  Performance Testing Information

There were also potential performance issues raised. A complete cycle for storing an article 
using Event Sourcing shall not take more than 150% of the time than it does using the current 
JModelLegacy in traditional 3.x 

## Reason

Using events instead of states for internal communication allows other parts of the software to 
react on these events in an adequate way. Storing is lossless.The impact of replaying the 
events on the last snapshot should not have much impact (will get tested first), but give a native 
versioning instead. Event sourcing is a design supporting change. 

## References

  - [Broadway](https://github.com/qandidate­labs/broadway)
   
## Discussion

*This is a collection of statements and comments on Glip regarding dependency injection container questions.*

Niels Braczek
>> "Why did you leave Event Sourcing out at the summary in Pythagoras?"
>
> Because there are no decisions about it but to look at a PoC. I see the value of Event Sourcing in a couple of environments, but not necessarily in a CMS, where one is mostly interested in the current state and nearly never in how one got there.
> And, since now there is discussion about it, it of course will get its own page. 

@2015-09-05 05:05
