# Universal Content Model

## Why bother?

  - Content consists of 'primitives' (headlines, paragraphs, images, ...).
  - Primitives are often grouped into more complex structures (author's bio, teaser, link list, ...).
  - The content structure is a tree of primitives and groups.
  - The tree must be serialised according to the desired output format.

Using the Composite Pattern on ContentTypes provides a Universal Content Model,
which easily can be serialised by a Renderer implementing the Visitor Pattern.

## Goals

  - Content MUST be format agnostic, so it can be served as HTML, JSON, plain text or arbitrary other formats
  - Components SHOULD NOT need to define their own ContentTypes,
    but be able to build them up from predefined ContentTypes instead.

## Options

The currently available options seem to be:

  - PHPCR
  - Own solution
  
## Comparision

There are several implementations of PHPCR.
They all have in common, that they handle much more than just the content tree
(eg., storage handling), which is not desirable for JoomlaX.

## Solution

**The Universal Content Model will be implemented as Joomla packages (Joomla\Renderer, Joomla\Content).
Content uses the Composite Pattern. Output is created using channel specific Renderers.**

## Tasks

  - The ContentTypes have to be defined, as they determine the Renderer interface.
  - The Renderer interface must contain a (deprecated) method to handle JDocument as a ContentType. 

## Consequences

**3PD** MUST rewrite their views to use the content tree instead of JDocument for Joomla(X+1).

## References

  - [PHPCR implementations](http://phpcr.github.io/implementations/)
  - [Content structure](http://nibralab.github.io/joomla­architecture/content­structure.html) 
  - [PoC for Renderer](https://github.com/nibralab/joomla­architecture/blob/master/poc/dynamic­renderer.php) 
