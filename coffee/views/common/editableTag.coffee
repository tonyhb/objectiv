define(["app"], (app) ->

  EditableTagView = Backbone.View.extend({
    className: 'editableTag',

    events:
      'blur' : 'blur'
      'keypress' : 'keypress'

    # Initializes the contenteditable tag. Valid properties to pass are:
    #   content:  The contnet to be shown to the user by default
    #   model:    The Backbone model owning any content we may be editing
    #   field:    The name of the field of the Backbone model being edited

    initialize: (properties) ->
      # Ensure it's there
      properties = properties || {}

      # Inner view initialisation
      @.innerViews = @.innerViews || {}

      _.defaults(@, properties)

    render: () ->
      # Sanity check
      @.content = '' if @.content is undefined

      @.$el.html(@.content)
      @.$el.attr('contenteditable', 'true')

      @

    blur: (event) ->
      return @ if @.model is undefined or @.field is undefined

      @.model.set(@.field, @.$el.html())

    keypress: (event) ->

      if event.which is 13
        @.$el.blur()
        event.preventDefault()

  })
)
