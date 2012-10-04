define(["app"], (app) ->

  EditableTagView = Backbone.View.extend({
    className: 'editableTag',

    # Content in the editable tag
    editable: null,
    content: undefined, # If this is an editable content-holding tag what is the initial content?
    attributes: undefined, # If this is an editable self-closing tag, which attributes are there?

    initialize: (properties) ->
      # Ensure it's there
      properties = properties || {}

      # Create the editable tag element using properties supplied
      @.editable = @.make(properties.tagName, properties.attributes, properties.content)

      @.tagName = 'div'

      # Inner view initialisation
      @.innerViews = @.innerViews || {}

      _.defaults(@, properties)

      # You can't set 'el' as an undefined tag from instantiation, so we're
      # saving it here to use when rendering
      @.parent = properties.el if properties.el isnt undefined

    render: () ->

      # Set the parent element, if possible
      @.setElement(@.parent) if @.el isnt @.parent

      # Are we adding text content?
      @.$el.html(@.editable)

      @

    click: (event) ->
      console.log("In place editable tag clicked", event)

  })
)
