# MVC Implementation

## Decision

The proposed command structure will be implemented.  (see references; naming according to 
GOF Design Patterns) 

A command decides which model(s) to use. It is responsible for making input available to the 
model and to add the output to the visitable output object graph. Thus, the model can be literally 
any object with public members ­ in theory even a J1.5 MVC triad or a non­Joomla solution. 
The output graph is transformed into a streamable format (according to content negotiation), 
and ­ if appropriate ­ wrapped into a PSR­7 response object. 

## Reason

The command/controller approach gives most possible flexibility for the implementation of 
models, so it is possible to integrate existing software. it allows to do proper CQRS, if wanted, 
by letting read (‘Query’) and write (‘Command’) commands use their own model. 
The renderer approach allows to serve any output channel (JSON, XML, HTML, PDF, ePub, ...) 
without the model or controller even knowing about that. 

## References

  - [Command structure](http://nibralab.github.io/joomla­architecture/command­structure.html) 
  - [Content negotiation](https://github.com/nibralab/joomla­architecture/blob/master/poc/renderer­factory.php)
  - [Renderer](https://github.com/nibralab/joomla­architecture/blob/master/poc/dynamic­renderer.php) 

## Discussion

*This is a collection of statements and comments on Glip regarding dependency injection container questions.*

Nicholas Dionysopoulos
> I intend to base my code on (refactored versions of) the [MVC I've written for FOF 3](https://github.com/akeeba/fof/tree/development/fof).
> This code is tested on humans, i.e. your average developers building the typical Joomla! components.
> The DataModel is heavily influenced by Laravel's Eloquent which while not an ORM is close enough for the level
> of developers writing Joomla! software (and over the head of quite a few of them, but they tend to manage figuring
> it out eventually – as I said, it's tested on humans).
>
> FOF 3 uses the dead simple MVC approach: Controller gets the data, sets the Model state, gets the View,
> assigns the Model to the View and tells the View to go render itself.
>
> As I understand it, my Model needs to be broken down to a. Service layer and b. Persistence (data access) layer.
> Semi-good news: the FOF30\Model\Model class is ALMOST a service layer and the FOF30\Model\DataModel class is ALMOST
> a data access layer. Except that the latter inherits from the former which is no bueno.
>
> Here's my problem. I have no idea what the Service layer should look like and how it would pass the session data
> (the model state as I have it now) to the Peristence layer. I'm afraid to come up with something in a field I have
> no experience with because I'm more than likely to screw up big time. Any pointers are welcome.
>
> Also, to make things explicit, the FOF30\Model\DataModel\Behaviour would be best implemented as horizontal
> components BUT that would require a whitelist. Pending a decision on how to blacklist/whitelist HCs I intend
> to only refactor it to use a private event Dispatcher object, basically replacing FOF30\Event with Joomla\Event.
>
> As for Controllers and Views, it's another thing for another week.

Niels Braczek
> Let's start with what we know.
> 
> Due to the Channel Independency Border, a component never gets access to the request. It gets an input object,
> which contains relevant data from $_REQUEST and $_SERVER.
> Also, the output is a media-agnostic object tree (Content Composite), which is handled by a renderer outside the
> scope of the component.
>
> We need a [DataDescription (based on JForms)](https://github.com/joomla-projects/joomla-pythagoras/blob/staging/discussion/jforms.md),
> containing all information needed for presentation, validation and persistence on field level.
> I have an idea about using this for a generic data structure (kind of Data Transfer Object) That will allow us to
> have a generic Finder, Validator, and Persistor as services (read and write are automatically separated!).
> The MVC would not have to deal with that itself. This triad will replace JTable in some extend, transparently opening
> for other persistence types than database tables. You could even store articles as markdown files in the filesystem.
>
> I tend to favorise the CommandBus, as it allows issuing command from both outside and inside the application.
> The FrontController (outside of component's MVC) has the responsibility to find the appropriate commands from the
> request and put them onto the bus (I already. started working on this)
>
> These are the borders of the area, which MVC has to fill.
>
>   - *M* Since the data model is handled by services, the Model merely is reduced to the business logic.
>   - *V* The View is made up of two parts: a) adding to the output tree, b) provide information (templates, layouts) for the renderer.
>   - *C* I'm not sure, if the Controller still is needed. What we need, is a collection of Commands, which of course could be public methods in a Controller.

Nicholas Dionysopoulos
> That's a MAJOR departure from what our developers understand and, frankly, a major departure from Laravel,
> Zend Framework etc they may be familiar with. Are we perfectly sure that this architecture fits Joomla!'s audience,
> especially integrators and developers?
>
> It's not what I understood when Chris was presenting web services in Denmark.

Nicholas Dionysopoulos
> I agree that the component should be isolated from the magic superglobals.
> I disagree that we should base everything around JForms.
> I can think of at least two components that would be impossible to build: Akeeba Backup and Admin Tools.
> Same for forcing a formulaic renderer common to all components and outside the component's scope.
> This is a great architecture for an enterprise CMS where another team will work with the designers to produce a consistent layout.
> But for our mass distributed project with uncoordinated 3PD components it would lead to either formulaic content
> that "looks like Joomla!" or exit from the market. Unless I'm missing something major here.

George Wilson
> I mean if you put everything in the renderer as JLayouts (which can be overridden per component) then it's still
> completely overridable and doesn't need to be "looks like Joomla!"

Niels Braczek
> Override will be per ContentType, not necessarily per Component, but can be position dependent.

Nicholas Dionysopoulos
> Well, yes, but this does require a content tree with a component and view specific content type and the relevant JLayouts.
> It sounds like extra work without much gain to me.

George Wilson
> I can see the benfits in the backend. I can see positives and negatives in the frontend.

Nicholas Dionysopoulos
> For formulaic content (lists) it's awesome.

Niels Braczek
> The gain is, that it enables output on any channel.
> CLI, eBook, WebService, ....

Nicholas Dionysopoulos
> For edit forms it's great if you use VERY formulaic content, a major PITA if you try to go out of your way like in
> Akeeba Subscriptions (subscriptions view), Community Builder etc.
>
> For non formulaic content like all views in Akeeba Backup and Admin Tools I have to write 3x as much code in 5x as
> many files to do the same thing, making it impossible to maintain.

George Wilson
> yeah my project i'm working on doesn't work either with JForms (i mean if i hacked the crap out of it i could it) -
> but there always needs to be the posibility of using custom html views

Nicholas Dionysopoulos
> Which isn't possible unless you have a plain HTML content type the renderer is aware of.
> Which kinda beats the purpose of a renderer?
>
> Regarding output on any channel:
> We currently have that format=something parameter.
> I am already using it in FOF to let you render content as JSON.
> I do see why you need to have structured content there. Same goes for rendering the same content in any other format, including basic HTML.
> I just disagree that we should not allow the developer to define the GUI to their application in HTML.
> So, let's step back a bit.
>
> There are two intentions behind rendering HTML:
>
>   1. Provide a GUI
>   2. Render content
>
> The first unfortunately needs raw HTML for various reasons (if you try to abstract it too much you'll end up writing the HTML and JS specifications from scratch).
> The second is a subset of "I want to render content in a suitable format"

Niels Braczek
> That's ok, the question is only, where it has to be. As I showed in Denmark, the renderer concept includes the possibility to use callbacks on the component's view.

Nicholas Dionysopoulos
> The first part belongs to a renderer, right?

Niels Braczek
> Yes

Nicholas Dionysopoulos
> What threw me off is that the renderer is not part of the component.
> But it has to, or components won't have a say on their UI.

Niels Braczek
> The component can *add* to the renderer.

Nicholas Dionysopoulos
> So I could tell the renderer "if you see com_akeeba content, call this first, then proceed with your other rules"?
> OK, that makes sense.
>
> I don't understand how the persistor service would work.
> And I have therefore no idea how to implement something like that. Not to mention that I don't have any code to
> offer in this case so what I said yesterday in the meeting is null and void: we can't use any of the code I have already written 

Niels Braczek
> The Finder, Validator, and Persistor Services are nothing we have agreed on yet; they are (from my point of view)
> logical consequences from the DataDescription, which is on the decision list.
>
>     $dic->get('persistor')->persist($dto);

Nicholas Dionysopoulos
> There is an unintended consequence of DataDescription that I was thinking about the last month.
> It would make single click migration impossible.
> But a single click migration is a stated goal with higher priority.

Niels Braczek
> That's right

Nicholas Dionysopoulos
> I'm also not sure if structured content would agree with our target audience, mostly because we have no personas defined and no user tests to give us insight
> For me that's a tentative Joomla! 5.0 goal.
> We need more experience and data to make an educated decision.
> If we DO go that way I agree that the architecture you explained above is perfect.
> And we'll have a hell of a time educating developers and integrators to use it, but that's another story. 
> We'd have to also change the way we're storing data.

Niels Braczek
> Agree so far. But why would an extended JForms XML make one-click-migration impossible?

Nicholas Dionysopoulos
> Extended JForms doesn't have an impact on migration.
> I'm using it since FOF 2.0 which makes it 2.5 years now.
> However, one thing I found out is that the extended JForms only allows for VERY limited content rendering.
> If you want to do anything besides the very formulaic back-end pages you need to use a proper templating system.
> Be it old school PHP templates, Blade, Twig, you-name-it.
> In theory you could do that with additions to the renderer, but this means that an integrator who wants to touch
> the system output needs to know PHP. We're not Drupal and we shouldn't try to be Drupal.
>
> What if we went for a best compromise for Joomla! 4 (because we need to deliver a product and build a foundation for easier future change)?

Niels Braczek
>> "Be it old school PHP templates, Blade, Twig, you-name-it." 
> Of course. But the fields could contain information about, which kind of control/widget/... the want to be rendered as. We have that as 'type' now.
>
> What I'm thinking about is to have two sections in the XML file: Data definitions (as needed for persistence and validation)
> and Views (list, detail, form). In the latter, you can define, which fields to show in which view, and how to show them ('layout' attribute),
> so you can show them differently in list and detail views.
>
> BTW: Introducing Finder, Validator, and Persistor Services does not automatically say, that the 'traditional' way will go away.

Nicholas Dionysopoulos
> So you want the XML file to be both a schema declaration and a GUI declaration?

Niels Braczek
> Not necessarily in the same file, but: yes.

Nicholas Dionysopoulos
> I can think of many, many cases where display and persistence have nothing to do with each other.

Niels Braczek
> FullAck.

Nicholas Dionysopoulos
> So I'm very reluctant to consider this a good idea, as you understand.
> I'd rather have a schema definition that's disconnected from the view file.
>
> IMHO the JForm XML is just a template language.
> If we view it this way we can just implement different view engines (renderers?) for plain old PHP view templates,
> extended JForm XML forms, Blade, Twig, insert-your-own-template-system-here.
>
> Which only leaves us with one question:
> what goes between the persistent data and the renderer?
> Isn't this something the view should provide?
> I'm thinking about how Laravel allows you to inherit and combine views.
> If I understand correctly, each Laravel view is a content type that carries both the data and the knowledge to render it in some format?
> Couldn't we do the same?

Robert Deutz
> not sure if it helps for the discussion; in Laravel you can add data to the view, that allows you access data
> (not only with blade) you can do in a controller something like return view('name')->with('varname', $value) and
> then in a template (again must not be a blade) <?php echo varname; ?>, the real cool stuff is that you can compose
> data when a template is used, that allows you some automatism and you have to carry data only when needed.

Chris Davenport
> Ross Tuck did a great presentation on service layers here: [DrupalCon Amsterdam 2014: Models & Service Layers; Hemoglobin & Hobgoblins](https://www.youtube.com/watch?v=ajhqScWECMo)
> He's also the developer behind [Tactician](https://tactician.thephpleague.com/).

Nicholas Dionysopoulos
> Thank you for the video!
> This is awesome stuff, but
> I don't think it's for the bulk of J! developers out there. 
> At best they'd end up with "Lasagna code".
> At worst they wouldn't know where to start.
> They can't even muster the oversimplified CRUD stuff we currently have.
> I don't know. I like the concept but if it's unsellable to the people who will be called to use it does it make
> sense to implement it? Or does it make more sense to have a glorified CRUD like Laravel?
>
> And how can we take such a decision without hard data which can tell us how much (and how many) people understand out of this architecture?

Niels Braczek
> Just saw the presentation. Great stuff. The best thing is, that CQRS and CRUD can be used in parallel, so we have a
> low entry level for average Joe Developer, but can offer good code for enterprise level.

Chris Davenport
> I think the key will be to refactor the core components to use the new architecture so people have good examples to 
> copy from (backed up by documentation that perhaps a few might read (I'm an optimist)).  Our code needs to set a good example.
> I think the mistake we have made in the past is that the core components have been left as "we'll fix that later",
> so everyone ends up copying our own bad practice.  Eventually, bad practice becomes so ingrained that people don't
> even notice it any more.  That's where we are with Joomla 3.

Marco Dings
> We also need to about some "training" in the form of these video's (but thats for later).

Chris Davenport
> I'm nearing completion on a project which I refactored to use a service layer after watching that video.
> It's amazing the difference it made.  It gave much better separation of concerns (particularly separating
> "infrastructure" logic, like sending email notifications, from true domain logic (the business rules) and allowed
> me to make changes to the software as the client moved the goalposts (as they do) much more easily.
> I started out with a custom command bus, but I eventually switched to using Tactician.  You end up with less code,
> which is actually a lot easier to understand because the separation of concerns helps to prevent it becoming spaghetti..
>
> Once you've used a service layer with a command bus you'll never want to write a component without one again,
> except perhaps for the really dirt-simple jobs that have almost no logic in them anyway.

@2015-09-04 03:50 UTC
