# Defines our app.
define(["jquery", "underscore", "backbone", "modernizr"], ($, _, Backbone, Modernizr) ->

  app = {
    api : '/api.json/v1/',
    currentSite : null,
    cache: {}
  }

  # Parsing should always use the response content and exclude metadata
  _.extend(Backbone.Collection.prototype, {
    parse : (resp, xhr) ->
      return {} if resp.metadata.status isnt 200

      resp.content
  })

  # Override some of Backbone's model defaults
  _.extend(Backbone.Model.prototype, {
    # Our ID field is actually '_id', therefore isNew doesn't work. Override
    # this to check for the correct field, allowing us to save and create models
    isNew: () ->
      this.attributes._id is null or this.attributes._id is undefined
  })

  _.extend(Backbone.View.prototype, {

    # Closes the view by cleaning all events then removing the container from
    # the DOM.
    close: () ->

      # Close all innerViews first; this will remove their event binds
      _.invoke(@.innerViews, 'close')

      # Stop any events from being delegated in jQuery
      @.undelegateEvents()

      # TODO: document?
      @.unbind()

      # Remove the element from the DOM
      @.remove()

      # If we have an onClose method with extra cleanup call it.
      @.onClose() if @.onClose

      @.innerViews = {}

    remove: () ->
      $(@.el).remove()

    unbind: () ->

  })

  # Always return our App object.
  app
)
