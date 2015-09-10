# Events

*This is a collection of statements and comments on Glip regarding events.*

George Wilson
> We've been toying with this and an implementation thereof [https://github.com/wilsonge/fig-standards/blob/event-inter/proposed/event-interface.md](https://github.com/wilsonge/fig-standards/blob/event-inter/proposed/event-interface.md)

Nicholas Dionysopoulos
> Pretty much what I had in mind. I was thinking of also adding support for caching in addListener
>
> When I'm firing an event to get the Application object I expect it to be cached. When I'm firing an `onContentPrepare` I expect it to not be cached.

George Wilson
> that would be a specific event object that follows the interface right tho?

Nicholas Dionysopoulos
> Yes
>
> Hm, the caching could be done internally you mean?

George Wilson
> I'm trying to think where you would cache it. Because generally the dispatcher isn't going to cache all the event objects that goes through it
>
> but you want to "create" an event object each time you have an event
>
> Even if you have "CachableEvent implements EventInterface" you would need to store that event somewhere 

Nicholas Dionysopoulos
> Actually the event object needs to exist to add it as a listener. I was talking about the event result.

George Wilson
> the listener gets added to the dispatcher. the event object contains the event result. or am i misunderstanding and we are talking the same thing?

Nicholas Dionysopoulos
> Never mind me, we're talking about the same thing.

Niels Braczek
> The dispatcher has to take care of caching somehow, since it has to know, if it should return a cached item or trigger processing.

Nicholas Dionysopoulos
> What if an event is handled by multiple listeners but not all of them are supposed to be cached?
>
> Example
>
> onAfterPageRender (random event name, OK?). I have one listener to add something user specific and another listener to add a Google Analytics code.
> The first is non-cacheable, the latter is.
> If I handle caching in the Dispatcher we have to treat the event as non-cacheable

George Wilson
> are we caching long term then. i thought we were just caching per page load
>
> at which point you know which user object you have for the full cycle

Nicholas Dionysopoulos
> I am trying to get an example on the spot, didn't have time to make up a _plausible_ example 

Niels Braczek
> From the dispatcher point of view, the event is not cacheable, correct.
> The Google Analytics listener has got to cache its own stuff.
> Dispatcher caching is merely made for service locating and alike stuff.

Nicholas Dionysopoulos
> OK, that's what I understood.
> I will have to play around with the idea a bit.

Herman Peeren
> I also see something with the event-handling happening that might be improved: a subscriber pattern (using an
> eventdispatcher as mediator between an object that triggers an event and a observer) can be used to completely
> decouple the event triggering object from the observer object. In that case the observer doesn't know who trigered
> the event and will not give any results back (and certainly not change any state of the triggering object).
> In Joomla this has always been mixed with hooks, which are a kind of subroutines. A system with hooks is much
> tighter coupled; it can even become a "plugin spaghetti". That is why I am looking for a better decoupled system.
> That will influence our content plugins etc. The way we use events or hooks is an architectural decision (which was
> not explicitly made). I have to go now, so I cannot work this out further now. Will come back this afternoon.
>
> I also saw Nic using the variable name $eventSource for the object that triggered the event. I think, with all
> talking about Event Sourcing, this is a confusing name. If we need to know that event triggering object (see
> paragraph above) then maybe we should have another name for it.

Nicholas Dionysopoulos
> As far as the variable name goes I can call it $source or even $clintEastwood as a temporary measure. Variable names change easily.
>
> As for the mediator pattern I know I will bore you to death but it has really bad performance.
>
> I understand that the event handler knowing what the caller is might be considered tight coupling but it is more flexible than no coupling at all.
>
> For example, implementing a modified by / modified on behaviour.
> 
> If the handler / plugin / whatever you call it knows about the model / whatever you call it it is possible to examine
> if it's a new record and whether a modified by / on is already set and modify these columns accordingly.
>
> In the fully decoupled state the even handler / plugin would have no idea about the internal state of the model.
> All it could return is a date an a user ID. Then this information would have to go through the mediator and back
> into the model. But the model MUST NOT use that information if it's a new record! So the model now needs to know
> which event handler replied and implement specific code to handle the part of the process the handler cannot handle.
> IMHO this is even tighter coupling and completely self defeating.
>
> The power of Joomla! comes from its plugins and from the fact that plugins KNOW about (or are simply passed) the
> objects they are meant to handle. If we change that I will be hard pressed to sell the new concept to anyone, including myself.

Herman Peeren
> "As for the mediator pattern I know I will bore you to death but it has really bad performance." That looks nonsense to me.
> To know is to measure. On what grounds did you make that statement?
>
> I think we disagree on a fundamental insight, mainly from functional programming: changing state is something that
> should be avoided. Immutability is the new hot topic in software development. I guess it will all come down to making
> good POCs of the opposing views on this and then take a decision. The tight coupling we now have in the Joomla is one
> of the things that you are used to very much, you probably even like it, but in my opinion it is one of the
> fundamental problems. Again here, I would like to have some room for disagreement and come with facts even if you
> "will be hard pressed to sell the new concept to anyone, including yourself". Innovation and learning comes with pain.
> We can ease that by offering good migrations and by showing that an alternative is realy an improvement (otherwise we
> don't have to do it).

Nicholas Dionysopoulos
> Sorry, but I don't understand what is the utility of an event handler which cannot manipulate the state of the caller.
> Just return a message? To do what? Doesn't this mean that the object raising the event needs to have hardcoded every
> possible way to be manipulated?
>
> And, honestly, if the new architecture means I have to spend 1 year for each of my extensions to work anywhere
> approximately as complete as they do today why should I rewrite them for Joomla! (3% marketshare) and not WordPress
> (20%) marketshare? I know the answer to this rhetorical question but 99% of developers out there don't.
>
> And anyway. How do I know that calling an object which returns another object that is parsed by the first or a third
> object to change the state of the first object is slower than directly changing the state of the first object?
> It's tautology. I can't see how it's even possible to have MORE code execute faster than LESS code. So there.
> Of course if you want to benchmark it please be my guest and at the same time maybe you can demonstrate how a simple
> "modified by / modified on" event handler would work on the old architecture and the one you have in mind.
> I know how the "old" architecture works because I've seen it in Nooku, implemented it in FOF 2 and fine tuned it in FOF 3.
> The new one I've never seen, can't understand and want to see at least a POC before I can contemplate if I should work on it.
> Can't implement something I don't know how it is supposed to look.

Herman Peeren
> NO: I didn't say you cannot implement YOUR vision on how things should be done. Quite the contrary: just do it!
> I only want some room for a different sound, for I learned the way some things are done now in Joomla are fundamentally wrong.
> But it is MY responsability to show a POC of that different way to do things, not yours. I want room for that and compare
> the different solutions, instead of just shooting off all alternatives before you even seriously looked at them.
> I very much dislike it that any (educated) contra sound is just shot off, especially if you say that it holds you from work.
> That is the same as "shut up, I need no alternative thoughts", for then I immediately resign from this group. 
>
> If you don't want to rework anything, then it is easiest to stay to Joomla 3.. Extra features can always be added0.
> The only reason to go to Joomla 4 is a break with backwards compatibility.

Nicholas Dionysopoulos
> I have VERY limited time, like you. I want to implement something once, especially if it's going to be fully Unit Tested.
> If I implement something in way X but you want it in way Y then I have just wasted my time and my only chance to implement it.
>
> So, I really need a POC of how a mediator would fit in this simple example.
>
> Also, it's not about sticking with my ideas. I thought that our job was to help the developers who are using Joomla!.
> If we try to shove something to their faces that they don't understand will they use it or just walk away?
> Sure, bleeding edge architecture is great but if nobody's using it it's not worth the space on the disk it consumes. That's my view.
>
> Again, I have no idea what I'm supposed to do. Implement something that you tell me is bad architecture and waste my time? Wait for someone / something?

Herman Peeren
> That would just be the same the other way around. Are you saying you don't want me to further explore paradigms like
> Event Sourcing and DCI? Because that can indeed have the result that those implementations could be superior and you
> programmed for nothing. And the other way around: if your implementationis accepted anyway or proves to be superior,
> then it has no use for me to spend any time to it. OK. Then I have to quit this group. Good luck with your next
> Joomla version. I'll work on mine (under the name of Joomla or a fork I don't mind).
 
Niels Braczek
> Nic, the EventDispatcher in fact already is a mediator 

Herman Peeren
> Yes, the EventDispatcher in in Joomla is a mediator, but in Joomla we also change state of the original object that
> triggered the event. It is a very handy way to use "subroutines", but it totally defies the good things of decoupling
> by a mediator/subscriber. That is rooted very deep in Joomla, so I understand Nic has a problem with it.

Nicholas Dionysopoulos
> Herman, I do want you to try out these new things and I think I made myself clear last weekend that I am interested
> in seeing them implemented EVEN IF they have a performance impact. Don't negate my words.
>
> Is there any example I can look at on Monday afternoon?

Herman Peeren
> Not a Joomla example. I have a pile of books on reactive and functional programming. Will have to translate some of
> it to Joomla (which I cannot do before Monday).

Nicholas Dionysopoulos
> Just a barebones example would do. I can strip down my idea to the essentials so we can have comparable code for benchmarking.
>
> OK, then I'll pursue an event dispatcher according to George and Michael's FIG proposal which seems to also fit with
> what Niels and I discussed and you (Herman) can refactor later.

Herman Peeren
> So please forget anything I said now about decoupling; I'll come back with examples and refactorings, but not now.

@2015-09-05 05:05
