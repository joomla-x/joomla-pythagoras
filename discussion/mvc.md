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

@2015-09-02 20:50 UTC
