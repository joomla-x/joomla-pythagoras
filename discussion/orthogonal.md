# Orthogonal Component Structure

## Decision
 
An orthogonal system is introduced, where two different kinds of components are distinguished: 

  - Vertical: Weblinks, Contacts, Users, Content, ... 
  - Horizontal: Versioning, translating, tagging, commenting, ... 

Any horizontal component works with any vertical component *out of the box* being themselves 
agnostic about other components. This way, com_contact, com_weblinks, even com_users 
would automatically have ‘inherited’ tagging and versioning in 3.2 / 3.3 without the need to 
change a single bit of code in these components. 

## Reason

This approach allows any component to take advantage of new capabilities that are designed to 
be used across extensions. The orthogonal structure is a design supporting change.

## Discussion

*This is a collection of statements and comments on Glip regarding the Orthogonal Component Structure.*

Niels Braczek
> When discussing the OCS, we said, that it should be configurable (enable/disable) for each combination of VCs and HCs,
> and that components may deliver a reasonable preset. We just haven't discussed yet, how that will be done.

Nicholas Dionysopoulos
> I have some ideas on that.
> Horizontal components could declare the service type they are offerring, e.g. "tagging", "commenting" etc.
> Each vertical component could have an XML file to turn off specific service types by default, pretty much like access.xml.
> This would allow, for example, five different developers to provide a "commenting" HC without every VC needing to know
> about all of them if it wants to have no comments as the default behaviour.
> We also need to think if we want to control HCs per component or per model.

George Wilson
> i'd say per view rather than per model.

Nicholas Dionysopoulos
> It is not an accurate way to deal with it.
> Since HCs would mostly apply to the model, not the controller/view.
> Also, if I have a subscription I don't want it to be commentable anywhere.
> It doesn't matter if I'm displaying it in the "Susbcription" view or the "User" view.

George Wilson
> but if you have a blog post you might want commenting in the "blog" view but not in the "users posts" view.

Nicholas Dionysopoulos
> But I may want the User object to be commentable at the same time.
> Yeah, I know, that's why fine tuning of HCs gets a bit hairy.
> And we're talking about comments which OK, they can be worked around (overrides for example)
> If it's something important like ACLs it gets really disconcerting

George Wilson
> yup, but that's the point right

Nicholas Dionysopoulos
> We have to figure out how to deal with this before we implement orthogonality.
> Especially if we're going to make ACL a horizontal component

George Wilson
> take a real life use case i've got. i have a list view of some users. but if i want to display a list of users waiting
> for a renewal (which the client want in a separate view with different acl levels - i repeat real life case i'm doing
> right now). it uses the same model and database table
> like commenting and tagging maybe can be done with a combination of template overrides and models. but acl needs
> to be controllable as views rather than models

Nicholas Dionysopoulos
> I agree, George. Cases like these are what I have in mind when I say I don't want ACL to be an HC.

Nicholas Dionysopoulos
> If I understand things correctly the horizontal components would be middleware as far as Tactician is concerned?

Herman Peeren
> That is exactly what I said and have been working on. The same with Matthias Noback's SimpleBus instead of Tactician. The same concept of middleware. I switched to SimpleBus also because I was experimenting with async message handling: for eventual consistency not all events have to be handled immediately, on the spot. For instance sending a mail. But also updating read-models. I started doing that with the promises from ReactPhp, but Simplebus also has https://github.com/SimpleBus/Asynchronous (not yet fully explored). It is the concept of a messagequeue like RabbitMq or ZeroMq, that you would use in a bigger enterprise application, but then simpler and even usuable on a shared host.

Niels Braczek
> I can see at least some HCs work as middleware, but most of them will have to react on events.

Herman Peeren
> Disagree. Important point to get right. Still think you want to misuse events the way it is done in Joomla now: as hooks. Events are simply messages about things that have occurred in the past. Any listener can do things with that, but it should not be used to change state of an "emitter". Like you see now with "onBeforeSave" etc. That is not a proper event. Then you couple things to much. 

@2015-09-05 05:05
