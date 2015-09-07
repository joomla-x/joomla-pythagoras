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

Nicholas Dionysopoulos
> Are we going full in and using a repository instead of having a model do db stuff?

Niels Braczek
> Repository would be best in terms of SRP, but we can introduce them later.

Nicholas Dionysopoulos
> Because the number of things I am not familiar with increases by the day and I don't think I can learn how to use all of that in a month, let alone start implementing them in code for J! 4.

Niels Braczek
> Take the View: It is obvious, that we must separate rendering from data retrieval, right? So our current views just get split into two parts: data retrieval (aka ViewModel) and rendering (in essence plugins (callbacks) for the renderer) and/or templates/layouts. The ViewModel does only retrieval, so it is close to read repository already. A bit of refactoring extracts the repository and leaves the ViewModel as an adapter. No big deal.
>
> For the Model itself, it has `findById` and similar methods. Encapsulate all db access that way, as generic as possible/reasonable. Same for write access. Then, as a second step, these can be extracted to the read and write repositories, leaving the original method as a proxy. No big deal, either.
>
> The hardest thing, I see, is the detection of commands. The first approach is to find all public methods of a controller. Next step would be to extract those methods into Commands.

Allon Moritz
> I guess I don't get here something right. From MVC aspect the View should NOT care about data retrieval, that's the job of the controller. The controller should fetch the data from the data layer, preferable trough the service layer if there is going to be implemented something like that. After the controller has the data, the view is feeded with the data. The view should only be for presentation and the model/service for data preparation. The controller itself knows the request (HTTP, command line, others) and can, based on the request, get the data the right way from the data layer (model/service). Or not? At least that's what I did in my day jobs, especially as I worked with Java.

Herman Peeren
> There are many implementations of MVC. Especially the role of the Controller differs. In one often used implementation the Controller retrieves the data from the model and gives it to the View. In Joomla the View gets its data from the Model. The word ViewModel is from MVVM, a MVC-implementation mainly used in the .NET framework, and comes down to a  Model as used by the View. When using CQRS there are 2 models: the write-model (for changing state) and the read-model (to retrieve stuff). The latter can be called a ViewModel, because it is only used by a View. The write-model doesn't need a View.

Allon Moritz
> Can then MVVM also work on command line? One of the problem with the actual set up on J3 is that the CMS almost can't be reused on command line. Most stuff just doesn't work when you want to build bigger cli apps in your component. Because things are so connected with each other, especially the model and the view. If that would be simpler, just having something like real MVC, then the model/service can be used anywhere ignoring the context it is in. If we are going to touch the MVC part, then I would suggest to decouple the view from the model and the controller as much as possible...Especially when we want to expand with web services.

Herman Peeren
> The trick with using MVC is: seeing it as a whole. So it has one public interface (always via some kind of controller) and the rest is hidden behind the interface (Demeter's law).
>
> You must be careful with "using things everywhere". That is one of the most difficult things in software development: you want to reuse stuff, but not let it becom a Big Ball of Mud aka spaghetti. 

Allon Moritz
> Agree, but from my understanding that's exactly what is not done yet. The interface (API) doesn't exist, view will be connected to the model.
> Definitely no spaghetti

Herman Peeren
> Globals come in disguise. For instance as singletons or as Service providers or Registries. You should especially take care with global state. A stateless service can be global (or static or provided via a Service Locator or whatever).

Allon Moritz
> But clear separation will make it for the average developer much easier than hard coupled views and models. I'm not talking about global states, directly the opposite

Herman Peeren
> Yes, but a view cannot be completely decoupled from a model, because it needs some  specific  data. Unless you would provide some semantics to those data.

Allon Moritz
> something like (in a controller): $this->view->greetings = $this->getService('Greeting')->getGreeting('hello');
>
> what do you mean with semantics?

Herman Peeren
> Yeah, that's a bit vague, sorry. I mean that the view needs some specific data and if you just feed it "data" it doesn't know what to do with those data, what those data are.

Allon Moritz
> Then you have other problems when the view doesn't know what to do with the data.

Herman Peeren
> The danger of using the controller in the middle to broker the messages between the model and view is that the controller can easily become too fat. Like you have in Symfony. Where the fatness of the controllers is often hidden in helpers (called "services").
> It doesn't matter so very much if you have a controller as broker between the view and the model. It is just another implementation of MVC. 

Allon Moritz
> This actually is MVC, if you don't have the controller, then you have MV 

Herman Peeren
> In Joomla the controller instantiates a view and a model that fit together. Then the view gets the data from its model. If you have the controller in the middle, passing those data, it is the same in the end.

Allon Moritz
> According to your statements, there is no plan to make the models reusable. 

Herman Peeren
> MVC is a pattern, not a library. That means you don't need a class that is called "controller. That role can also be played by a component-class with a command bus.
>
> About reusable models: "antipattern" or "codesmell" was not the right word. I mean: it sounds like an architectural flaw, a mistake in your design. Of course you want to reuse what is reusable, but a model should be something very specific for a context (for a component in Joomla's case). When it is not specific, you should extract the reusable part out of it and make that another thing. 

Allon Moritz
> So what do you suggest then, when I need to get an article in my component? Should I go directly to the database or use the model of com_content?

Herman Peeren
> Something else about composition: behaviour is better composable than objects. That is one of the reasons why the functional programming has gained so much popularity the last years. And that is one of the lessons we can learn from FP.
>
> Lets first get the problem clear. For now I don't completely understand you. 
> I have the idea that you are mixing some concepts.
> Could you elaborate a bit on "when I need to get an article in my component".
> What is the use case?
> Do you mean HMVC?

Michael Babker
> i think the issue he’s getting at is that our entire MVC structure right now is pretty locked to the web interface (which i guess makes sense considering that CLI support is barely present), so it’s rather difficult to reuse ContentModelArticle (for example) in a CLI environment… so whether it be a model (as we know it now), or a repository (i guess in the sense of Doctrine or Laravel), there should be a layer that can be used regardless of the environment

Niels Braczek
>> "Still think you want to misuse events the way it is done in Joomla now: as hooks."
>
> I understand your point. But you may not forget, that these hooks helped to bring Joomla! to where it is today. It is an easy to understand concept, that our average developer understands. Moving too far from that with one big leap would do more harm to the project, as it would benefit.
>
> My intention is renew the engine under the hood, without changing too much in places, where the average user/developer gets in touch with Joomla. CommandBus? Yes, but with a concept, that can extract commands from a controller, we know from J!3. New components, written by well-educated developers, should be able to do it right(tm) anyway..
> CQRS? Yes, but no forcing into it. ReadModels and WriteModels are perfect for us, but as I learned in the discussions here, we cannot expect that from our average developer. Thus we enforce a separation of read and write access to the database to start with, so we gain scalability from the beginning, but leave real CQRS to those, who understand it, until we have educated our developers accordingly. In J!5, we can be more strict, after having deprecated the 'old' stuff in J!4, and having given the developers some time to get familiar with the new concepts.

Allon Moritz
> Or in DPFields I have a an "article" custom field, which saves the article ID. To display the article title (or even more of the article) I need in DPFields get the article itself. How should I do that in J4?
> There are many use cases where somebody needs something from another component.

Herman Peeren
> If you need something from a component (say from com_content) then you should ask that component for it. Not an object (like a model) that is part of that aggregate. Like when you call a dog, you don't call the separate legs. You should program to the interface. This is an important issue, I think, about how we use our components.

Allon Moritz
> And what is the interface of com_content?

Herman Peeren
> Now, that is a good question! There is none at the moment. There should be one is what I say.

Niels Braczek
> Currently, the "interface" is made up from the public methods of the model. And that's not too bad, even if it could be better.

Allon Moritz
> And now the model should be more tight to the view, then we loose the interface completely

Daniele Rosario
> i dond’t totally agree about the “you should ask the component for the list of articles” part. IMHO we should ask the component for the MODEL, which should give it to us, and we query it. Something like
>
>   1. Get the Component (or it’s DI Container?) $container = \Joomla\Component\Content
>   2. Get the model from it
>
> $container->factory->model->get(‘Articles’)

Allon Moritz
> If we would have something like that would be cool. If you do that right now (J3) (ignoring the request) it crashes.

Herman Peeren
>> "Currently, the "interface" is made up from the public methods of the model."
> It should be the public methods of the controller: the role of the controller is interaction of the MVC-triad with the outside world: the input comes into the controller and should not go directly into a model.
>
>> "$container->factory->model->get(‘Articles’)"
> That is a nice example of the use of globals (in disguise of a service locator). Especially when that object also has state (retrieving a specific article in that way), we are back to global $mainframe. 
> Global $container is very convenient. But that is not the right criterium.

Allon Moritz
> What do you suggest then?

Daniele Rosario
> Herman Peeren it’s not global, it’s the container of the component, which you just constructed. It’s one per component, and you can create it from scratch. See FOF3 for examples of this

Herman Peeren
>> "If you do that right now (J3) (ignoring the request) it crashes."
> If you want to reuse components in a HMVC-way you will have to decouple them from the application context. They should not automatically use the global JInput. Retrieving a specific article from a com_content component needs to give the criteria to find that article to com_content. In other words: give a context  in which that instance of com_content lives.

Niels Braczek
> Herman, I'm interested in an answer, too: If we have a CommandBus (no return values) and Domain Events (no return values), how do I get access to, let's say, a user record in a subscription component?

Herman Peeren
> It can be OK if a specific container is given to a component, but often it is just a "black hat" from which all kinds of globals can be retrieved. In FOF for instance I have doubts about the session that is in that container. I don't necessarily say it is wrong, but it is a code smell by which we should be alert.
>
>> "how do I get access to, let's say, a user record"
> you should not retrieve a record in a subscription component. Within the component you should send a message to the model to retrieve an object (of which the data can be filled from a database). From without that subscription component there are no records.

Niels Braczek
> Which model? You said, I'm not allowed to talk to the user model. At least it is what I understood.
>
> I have a component, let's say, a subscription component. This component needs access to the user data, as well as to his permissions.
> While the latter can be served by asking the ACL service for a user's permissions, or by Events to elevate/reduce permissions, I need return values from a 'foreign' component from time to time. Let me try to send a notification mail to the user. Ok, my subscription component is not the right place to that, so I issue an event. Thus I got the need for the user data out of my subscription component. But that just moves the problem to a notification service. However, at one point, I need to get that damn email address from outside the user component. This is just one example, I'm sure, Nic could give you fivehundred more without having very much to think about it. My solution would be to put a UserRepository into the container, and just say
>
>    $userDto = $dic->get('UserRepository')->findById($userId);
>    $email   = $userDto->getEmail();

Nicholas Dionysopoulos
> Regarding session in FOF 3's container: it's a Bad Idea, I agree with that and I know it already  I cannot refactor it out because Joomla! 3 uses the session to persist model state AND keep track of transient user data when the validation fails. If there was another mechanism for session persistence I wouldn't have to use the session. The session is in the container in the hope that it can be refactored out (e.g. if a PROPER cache is implemented at some point and not the badly performing, unstable hot fuss we currently have). It would have helped if I wrote a code comment about it, but alas!
>
> Also, yeah, about what Niels said: if I start dissecting my code –especially Akeeba Subscriptions and Admin Tools– I can give you countless examples of needing to get data from a foreign component that's just so much easier to do by speaking directly to models. For example, I need to expire 50 subscriptions and send each one subscriber a notification email. I can send 50 "send an email" messages which results in 50 requests to the user repository to fetch the email addresses. Or I can do eager loading of the many-to-one User relation of my Subscritpion model to only perform 1 query. Stupid example, but the same performance issue is exaggerated in other uses cases, like articles which are linked to users and categories and tags and versions and whatnot.

Allon Moritz
> I guess we are all talking about the same just with different words.

Nicholas Dionysopoulos
> Actually I DO need to know the user's email address and username when constructing the message because I have to make my message idiot-proof in the format "Dear, Wiley Kayote, you have subscribed to Acme Inc's Road Runner Blaster Service as wilekayote and email address wilekayote@example.com" 

Niels Braczek
> I have all user data, including email address. in one place: the user component. If not my subscription component needs the email address, the mailing component does. In the design you explained so far, I have no chance to get the email address from the user component, which is the only place for the email address to be..

Herman Peeren
>  In different bounded contexts ("components" in Joomla") you can use the same data as basis. Are they duplicated? No: because they are used in a different bounded context, so they (can) have a slightly different meaning. For instance: if I have a subscriber (I try to avoid the general word "user" as that is merely related to the domain of drugs or computer use; but ok, lets call it a "user" for now), in the subscription component,  I probably don't have to know her e-mailaddress or some other details. While at the mailing component I don't have to know much about subscriptions. I just want to know what message must be sent to what user. And so a message is sent to the mailing component to send message x to user y. The subscriber component doesn't know what mailing component handles the command to send a mail to the user and the mailing component doen't have to know what component issued the command. 
>
> Or more general, you would not even send out a command to a mailing component but have for instance a SubscriberRenewedSubscription Event (containing all relevant information like subsciber-id and subscription-id) and have dedicated components to deal with that message, like store it in an Event Store or compose a message for the general mailer component.
>
> If you want to provide a general service for user-information, providing e-mailaddresses etc., that is possible too: then you will have to send a command (not an event!!!) to the API of that user-component to get the information back. It is purely functional: no side effects: data in, data out. You ask for an email address of user y, you get email address back. The foreighn service doesn't change any state of the component that uses the service.

Niels Braczek
> Commands are defined to have no return value. So how do I get the data?

Nicholas Dionysopoulos
> I get this, but... My messages need to know stuff from a. the subscription b. the custom field / extra information plugins c. the user and d. the site itself (like the site name, URL, etc). I don't see how processing the message in 4+n places is more efficient or easier to maintain than processing the message in exactly one place. Also, why would the user component need to know how to handle my subscription message? Isn't this tying my hands behind my back? If I want to do something the user component's developer hasn't thought of what I'm to do? That and the no return value are the major things I currently don't understand.

Herman Peeren
> Not necessarily true. Commands can have a return value. You have to make a difference for commands within the application, within the component  and from the outside. 
>
> From the outside, over HTTP, it is controversial whether commands should have a return value or are "fire and forget". That is a whole discussion and has diferent sides; for instance is a 200 OK an answer or not. 
>
> System wide, on the application level it can be done in different ways. But on the component level, with an internal command bus in a component, my idea is certainly that you can have return values. 
>
> It is about scope. Limiting the trouble.

Niels Braczek
> Ok, but the point is that I need data from another component, which I already know (at least the interface of). That is coupling, ok, but that coupling is intended. I want to couple subscriptions with users. So, if I know, that the user model offers a 'findById' method, it should be ok to call it directly.

Herman Peeren
> I think it is not OK to call that model directly, but that components (like a user-component) should have an API that you can access as a service. You should not know how the user component retrieves that user data (like a findBy-method in a model) but it just returns the data.
>
> I see the component as an aggregate with one entrance and one interface,  but you use the constituent objects in the component, like a model,  and use their public methods. Maybe the component I want to get some information from has no model . I don't care, as long as there is an interface to get the information from.

Niels Braczek
>> "that components (like a user-component) should have an API that you can access as a service"
> That's exactly, what I showed with the UserRepository.

@2015-09-05 05:05
