# List of Events

This document lists all events supported by the Joomla! core.


## Rendering

### onBeforeRegisterContentType

The `onBeforeRegisterContentType` event is emitted before a new ContentType is registered to a Renderer.
This event provides a `Joomla\Renderer\Event\RegisterContentTypeEvent` data structure.
It is a `Joomla\Event\Event` object with the following parameters:

Parameter | Type | Description
--------- | ---- | -----------
`type` | string | The name of the new ContentType
`handler` | callable | The handler for the new ContentType

### onAfterRegisterContentType

The `onAfterRegisterContentType` event is emitted after a new ContentType was successfully registered to a Renderer.
This event provides a `Joomla\Renderer\Event\RegisterContentTypeSuccessEvent` data structure.
It is a `Joomla\Event\Event` object with the following parameters:

Parameter | Type | Description
--------- | ---- | -----------
`type` | string | The name of the new ContentType
`handler` | callable | The handler for the new ContentType

### onRegisterContentTypeFailure

The `onRegisterContentTypeFailure` event is emitted after failing to register a new ContentType to a Renderer.
This event provides a `Joomla\Renderer\Event\RegisterContentTypeFailureEvent` data structure.
It is a `Joomla\Event\Event` object with the following parameters:

Parameter | Type | Description
--------- | ---- | -----------
`type` | string | The name of the new ContentType
`exception` | Exception | The exception that was thrown

### onBeforeRender\<ContentType>

The `onBeforeRender<ContentType>` event is emitted before a ContentType is processed by a Renderer.
This event provides a `Joomla\Renderer\Event\RenderContentTypeEvent` data structure.
It is a `Joomla\Event\Event` object with the following parameters:

Parameter | Type | Description
--------- | ---- | -----------
`type` | string | The name of the ContentType
`content` | Joomla\Content\ContentTypeInterface | The ContentType instance

### onAfterRender\<ContentType>

The `onAfterRender<ContentType>` event is emitted after a ContentType was successfully processed by a Renderer.
This event provides a `Joomla\Renderer\Event\RenderContentTypeSuccessEvent` data structure.
It is a `Joomla\Event\Event` object with the following parameters:

Parameter | Type | Description
--------- | ---- | -----------
`type` | string | The name of the ContentType
`renderer` | Joomla\Renderer\RendererInterface | The output renderer in its current state

### onRender\<ContentType>Failure

The `onRender<ContentType>Failure` event is emitted after failing to process a ContentType.
This event provides a `Joomla\Renderer\Event\RenderContentTypeFailureEvent` data structure.
It is a `Joomla\Event\Event` object with the following parameters:

Parameter | Type | Description
--------- | ---- | -----------
`type` | string | The name of the ContentType
`exception` | Exception | The exception that was thrown
