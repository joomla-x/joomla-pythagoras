# ACL

*This is a collection of statements and comments on Glip regarding general ACL questions.*

*See also [Orthogonal Component Structure](orthogonal.md).*

Niels Braczek

> I support dropping access levels in favour of view permissions.
>    
>>      -------- Weitergeleitete Nachricht --------
>>      Betreff: [jcms] [RFC] - Access Levels Joomla 4
>>      Datum: Fri, 31 Jul 2015 08:29:09 -0700 (PDT)
>>      Von: Justin Seliga <justinrseliga@gmail.com>
>>      Antwort an: joomla-dev-cms@googlegroups.com
>>      An: Joomla! CMS Development <joomla-dev-cms@googlegroups.com>
>> 
>> Access Levels are a bit rigid.
>> 
>> Joomla 4 provides an opportunity to improve the implementation of assigning
>> view access without worry of b/c.
>> 
>> A few shortcomings of the current implementation:
>> 
>>   1. In sites with more complex User Group hierarchies, I've seen
>>      situations where 5+ Access Levels share the same set of User Groups and
>>      only vary by one. This quickly becomes a manageability nightmare since an
>>      Access Level needs to be created for every combination of User Groups. (see
>>      Note at the bottom for an explanation)
>> 
>>      An alternative would be to do away with Access Levels.
>>      When granting view access on an item a user would select one (or
>>      multiple) User Groups in the "Access" field.
>>      The row in the database would hold a comma separated list of User Group
>>      ids instead of the one id that represents one Access Level (which
>>      translates to multiple User Groups anyway).
>> 
>>   2. Access Levels do not allow for view access to be inherited to
>>      children User Groups, they are explicit only to the groups within the
>>      Access Level.
>> 
>>      An alternative to this is to completely remove the concept of Access
>>      Levels as well as the "Access" field on items. Utilize a "View" (core.view)
>>      action in the Permissions. By leveraging the ACL viewing can now not only
>>      be explicitly declared per User Group, it can also be inherited through to
>>      child User Groups.
>> 
>> Though the first implementation eliminates the potential for an exorbitant
>> number of Access Levels it's not a feasible approach for allowing
>> inheritance.
>> 
>> The second alternative solves both shortcomings.
>> 
>> Note:
>> 
>> Assume there are only 4 User Groups in a Joomla site, to guarantee that
>> every combination of User Groups is accounted for 15 Access Levels need to be created.
>> If just one more User Group is created an additional 16 Access Levels need to be
>> created to cover the new potential combinations.*

Nicholas Dionysopoulos
> I disagree. In practice I have found that Access Levels are more practical than individual group selection.
>
> In fact, we should extends Access Levels; they are almost what you'd call "roles" but limited to viewing stuff.
>
> Practical example.
>
> Acme Corp builds a site. Among other areas there is the Strategic Roadrunner Annihilation section of their documents.
> This document currently needs to be visible by Coyottes and Mad Scientists.
> The same access is required for the Project Schematics and Roadrunner Hunting categories.
>
> Problem: we forgot to let the CEO have access to these documents.
>
> With Access Levels you just add the Top Management group to the View Level.
> Otherwise you have to remember which categories and documents you assigned to each group and amend them.
> On my very small site I can't remember that. On a large site I think it'd be impossible 
> So while it's yet another step and a bit slow it's very practical for our users.

Niels Braczek
> With ACL, you just add the CEO to the 'RoadRunnerSpecial' user group, as you would with the view level.

Nicholas Dionysopoulos
> Niels, that's good until you have 43,000 users 
>
> This is a real example, when I launched the Akeeba Solo beta.
> After the beta I had to revoke access except to those who had paid.
> With user groups I'd still be writing a script to do that 

Nicholas Dionysopoulos
> As I'm putting more thought into this, wouldn't you say that it'd be best to do away with hierarchical groups and have a flat user role structure?
> I can see some performance benefits that are hard to ignore...

Marco Dings
> hierarchical groups are more intuitive and user friendly i'd say

Niels Braczek
> At this stage, I'd say that implementation details are out of scope.
> My objective is to describe ACL on an abstract as possible level, so we (or others) can provide different approaches.
> From the discussion I learned, that the needs are very different, as are expectations.
> I though still have the feeling, that it should be possible to define an interface, that satisfies all of them.

Nicholas Dionysopoulos
> OK. But that makes it impossible to come up with a usable interface.
> Right now I know that there must be a Suepr User group.
> My interface depends on being able to provide a list of Super User users.
> If I cannot know for sure that there is the concept of Super User my interface will have to let you pick any user,
> even if the user doesn't have the necessary authorisation,
> which can only be discovered in the runtime.
>
> From a UX persepctive I'm a dumbass for letting my user pick the wrong data without pre-validation of the field.
> I need to have a minimum of assumptions about how the ACL works.
> Otherwise I will end up describing a system which can only answer questions about whether a user has a privilege for
> an asset (or no asset) and backwards (e.g. which users have a privilege for an asset / all assets).
>
> Is that practical? Somewhat.
> Will it make sense as far as performance is concerned?
> Um...
> I guess I can provide you the draft of the interface I have in mind and we can see if it makes practical sense later.

Robert Deutz
> I would start with the assumptions you think you need, I found often within the process that I can drop more and more of them.

Niels Braczek
> Even if we would remove users, there MUST be a differentiation between admin and others, of course.
> In most use cases, you just want to know, whether or not the current user is allowed to perform task A to asset B.
> That can be done transparently by a Horizontal Component. Other use cases, I can think of, are the need of lists of
> triples (user; task; asset), where you only know two of the components: All users allowed to perform task A on asset B
> (think of notifications), all tasks, that user C can perform on asset D (think of menus), and all assets, on which
> user E can perform task F (think of batch deletion). Maybe this does not cover everything, but it covers all I see
> for the moment - that's why I'm asking.

Nicholas Dionysopoulos
> I wrote a huge interface, then I sat down, rethought of it and I tend to end up with something VERY similar to what you said:
>
>     userHasPrivilege($user, $privilege, $asset = null)
>     getUsersByPrivilege($privilege, $asset = null)
>     getPrivilegesByAsset($user, $asset = null)
>     getAssetsByPrivilege($user, $privilege, $root = null, $maxLevel = null, $nodeType = 'all') // all, branch, leaf
>
> The basic bits we need are users, assets and privileges.
> userHasPrivilege would be the most often used method as it positively checks if a user (even the current user)
> has a privilege against all or a specific asset.
> getUsersByPrivilege answers the questions of who can do something to an asset or all assets.
> So if I need all Super Users I can do something like getUsersByPrivilege('SUPER').
> getPrivilegesByAsset would be a faster alternative to userHasPrivilege but I'm not sure it belongs to an interface.
> I can always loop userHasPrivilege to get a list against known privileges.
>
> I have not found a use case for having to discover which privileges are granted without knowing the privilege names beforehand.

Niels Braczek
> Menus

Nicholas Dionysopoulos
> Right.
> getAssetsByPrivilege would be the second most used method assuming that read access is also an ACL privilege as it should be.
> Essentially, I want to know, say, which categories and content items I have access to.

Niels Braczek
> With ACL as a HC, you only get those, you have access to.

Nicholas Dionysopoulos
> I disagree with that.
> As a developer I might want to touch things which lie beyond the reach of the user.
> Stupid example: hit counters.
> The user shouldn't be able to write to the content item but I have to if I want to increase the hit counter.

Niels Braczek
> Hit counter is not content, it is stats
> And you don't hit content, you're not allowed to see.

Nicholas Dionysopoulos
> Or, in my subscription system. Users can't write directly to the subscription of another user BUT my plugin needs
> to run in User A's context and unpublish expired subscriptions for users B, C, D and E.
> User A cannot see these subscriptions or handle them directly.

Niels Braczek
> Why does it have to run in user A's context then? It is a user-independent system task.

Nicholas Dionysopoulos
> Because that's how plugins work, they run inside the app context.
> We can't assume that the server has any sort of CRON support.
> Half of my users don't get CRON at all.
> If we start applying arbitrary .php endpoints to launch system tasks without CRON we're screwed.
>
> Also, in the same subscription system: if a coupon code is used by a specific user I want to unpublish it immediately.
> However, this user MUST NOT be able to unpublish coupon codes directly. Even worse, the coupon code may be used only
> once by either user A, B or C. And user B may not already exist but I only know his email address.
> If I have access to any record I want I can work around that and provide this feature.
> This is why I disagree that ACL should be an automatically imposed thing everywhere.
> The business logic may span different contexts.

Niels Braczek
> Well, then the problem is not ACL as a HC, but the (yet) missing system task layer.

Michael Babker
> take it from someone on team releasing an app which depends on cron jobs, that is the hardest area to get users
> to work with or have shared hosting support for… our top two questions on forums relate to getting the crons to
> actually execute and needing a script that is web accessible to trigger the cron because the host’s cron support
> triggers a web request (they allow system() or exec() to be used but not PHP from CLI.

Nicholas Dionysopoulos
> I think they are two different things, applying to two different classes of problems.
> I'll put it simply: if it needs CRON to work, I don't want to be part of building it.
> Been there, done that, got the t-shirt, lost my hair.
> Also, not ALL problems can be solved with a system task.
>
> If a developer can't figure out how to work within the limits of the system you know what will happen: hardcoded SQL queries like it's 1998.

Niels Braczek
> That should not keep us from making a clean design. We have to solve the problems mentioned, of course,
> but I don't think that a bucket is the right solution for a leak in the water pipe.
>
> For each combination of VC and HC, it will be possible to enable or disable the cooperation (that's part of the OCS).
> It is feasible to have a special plugin type, that is not covered by ACL, which would solve your issue.
> Other possible solution: The plugin could run as user 'system'. Having such a mechanism would additionally allow
> admins to view/use the site with the permissions of an arbitrary user.
> (Just raw ideas, haven't spent many thoughts on them yet)

Nicholas Dionysopoulos
> If my non-plugin code needs to access a resource that's normally unreachable by the currently logged in user BUT for
> whatever reason I have to access what do I do? Direct SQL queries? Back to Joomla! 1.5?

Niels Braczek
> What's the use case?

Nicholas Dionysopoulos
> Another example from Akeeba Subscriptions.
>
> When I'm getting a list of subscriptions I check for just-expired subscriptions and update them.
> I do get a list of subscriptions when evaluating coupon codes.
> So, from the context of a Guest user I might want to expire subscriptions of users B and C so the next time I have to
> run the same validation and query the same subscriptions I do not have to re-check the expiration of these
> subscriptions (it actually does help improve performance).
>
> While I could delegate this to a system task, I can see two problems:
>
>   1. I have to do the same expiration checks on the same objects again and again and write extra code to weed out
>      subscriptions which have enabled = 1 but are really not enabled for the context of my action (dirty, error-prone code)
>   2. I have to rely on the user setting up CRON jobs or not disabling a very important plugin which experience shows
>      is a very unreasonable expectation (unfortunately)
>
> Another example:
> I have a paid blog.
> Some articles are free, some are not.
> I want to show all titles to all users, but if the user is a guest (not paying) I want to show a "BUY NOW" button instead of the article.
> How do I do that? Different privilege for reading the title, the content etc?

Niels Braczek
> Could be a solution, but obviously not very handy...

Nicholas Dionysopoulos
> Yeah. Which is my concern.
> I can see the difficult solution and imagine how to code it.
> I am not so sure if the bulk of developers out there are at this level. They are certainly not at your level.
> *I* am not at your level and I'm part of this architecture group. See where I'm getting at?

Niels Braczek
> Getting your point (slowly). That is exactly, why I want ACL to be an HC, so the average component does not have to take care of it.
> I have to find a way to give experienced developers as you what they need, but also provide ACL out of the box for not-ACL-aware extensions.

Nicholas Dionysopoulos
> Yes, that's what we need.
> I have no idea how to get there.

Niels Braczek
> At least, we can agree on that requirement as a starting point. Even if it does not look like that, it is a great achievement.

Nicholas Dionysopoulos
> I have an idea regarding orthogonality and the need to not apply ACLs under some circumstances
> The horizontal components are implemented as plugins. We essentially "call" them through Events.
> What if we passed a blacklist pararameter in the event which would tell HC plugins when not to trigger.
> So, when my blacklist is ['acl', 'commentable'] I am preventing comments and ACLs from executing, but not any other HC.
> This still doesn't answer the question of how do I get a list of users who have a specific privilege on a specific asset.
> If I get an idea on that I'll share it.

Niels Braczek
> I'm not very fond of a blacklist. I'd prefer to establish Contexts, so you can say:
> "I want to execute this piece of code in system context, ie., not the user's".

Nicholas Dionysopoulos
> What if I want to have versioning but not ACL?
> The context is an all or nothing approach.
> Architecturally, yes, a context is best since you can disable specific HCs per context.
> This works best when you have an actual IT team running the site, able to create additional contexts should the need arise.
> I am just not sure that in our mass distributed application they will be practical despite being an architecturally correct solution.

Niels Braczek
> Basically, there are only two contexts: the current user, and the system (stuff you (c|w|sh)ould do with cron).

Nicholas Dionysopoulos
> Hm, I'm not sold on the idea.
> It also doesn't deal with the problem of an extension not wanting to have, let's say, comments or history.
> Right now we deal with it sideways, by not providing a content type.
> But if I wanted my extension to have comments but not history I couldn't do it.

Niels Braczek
> That's up to the site's configuration, not the component.

Nicholas Dionysopoulos
> Yeah, I would have to ask the user of the site.
> This brings me back to whether I want to the user to be responsible for that or not.
> I can foresee this situation:
>
> Integrator installs extension. Does not read the fine manual (OF COURSE!). Extension doesn't work.
> Integrator A files 1 star review on JED claiming extension is broken. Developer says "fuck this shit" and adds
> installer code which turns off all HC for his extension except the ones he knows for sure can work with it.
> Integrator B files 1 star review on JED because extension no longer works with his Super Duper Alternative
> ACL System for Joomla! 4. Developer leaves Joomla! and takes up something less stressful, e.g. minesweeping.

Niels Braczek
> When discussing the OCS, we said, that it should be configurable (enable/disable) for each combination of VCs and HCs,
> and that components may deliver a reasonable preset. We just haven't discussed yet, how that will be done.

@2015-09-02 20:50 UTC
