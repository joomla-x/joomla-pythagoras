# Dependency Injection

## Decision

Use Dependency Injection (Containers) wherever possible. Avoid Service Locators, if possible. 
Global services are requested by raising an event (e.g., ‘requestLogger’), which returns a 
logger. 

## Reason

With Dependency Injection, the provided services and objects are controlled by the calling 
instance. Dependency Injection Containers may help to keep the signature of constructor short, 
if several dependencies are required.  

Service Locators are appealing on first sight, but tend to turn into an anti­pattern, as they 
provide global references and cannot easily be tailored for special use cases (HMVC, for 
example, may need different services for subsequent requests). 
Requesting services using events decreases coupling. The event dispatcher is responsible for 
caching the result of those events, so there is no performance impact. 

## Discussion

*This is a collection of statements and comments on Glip regarding dependency injection container questions.*

Niels Braczek shared a link
> Is this something we could use? [thecodingmachine/picotainer](https://github.com/thecodingmachine/picotainer)
> picotainer - A minimalist PHP dependency injection container compatible with ContainerInterop

George Wilson · via mobile
> Is there any reason not to use the framework di package?
> I mean unless there's a important reason why a framework package fundamentally sucks. I'd rather use the framework package.

Niels Braczek
> Agree - I didn't look at the framework DI initially. AFAICS, it lacks of two things: implementing the InterOp interface
> (just missing has()), and the delegate-lookup feature. That would give a lot of flexibility for adding non-Joomla-3PD-stuff.

Michael Babker
> we have an exists method and i’d have to wrap my head around the delegate lookup thing to figure that aspect out,
> but our container does support “parent” containers

George Wilson · via mobile
> There is a method that we don't meet the interface for as well (can't remember which but we have two Params and they have 1).
> The exists method we have and their has method is the same iirc.
> This is the same interface that's part of FIG's PSR-11 fwiw.

Niels Braczek
> Yes, that was what lead me there.
> The InterOp interface just defines get($key) and has($key)

Michael Babker
> moving exists to has to use the interface isn’t an issue for me, but there’s some functionality with our get method
> and that second param that we’d need to make sure we don’t lose to make that change

Niels Braczek
> Doesn't it make sense to extract that functionality into an own method (like recreate($key) or something similar)?

Michael Babker
> i’d be good with that

@2015-09-05 05:05
