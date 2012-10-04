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

    # Child views
    #
    # There are two child view properties: children and innerViews.
    #
    # Because inner views can't be bound to a parent view until the element
    # exists in the DOM (setting the 'el' property calls setElement, which looks
    # for an existing element or creates one) we need to have a way to remember
    # which elements a child view needs to be bound to once the parent is
    # rendered.
    #
    # The 'children' property stores all unbound, unmade children which need to
    # be rendered from renderInnerViews(). This should not be touched, really.
    #
    # The innerViews property stores all bound and rendered views. 

    addChildView: (items) ->
      @.children = @.children || {}

      # Assign the element it's view in the @.children property
      _.each(items, (view, el) ->
        @.children[el] = view
      , @)

    # This is called from parent elements that have a template structure for
    # their child views.
    renderInnerViews: () ->
      @.innerViews = @.innerViews || {}

      # Loop through each inner view and set the correct element.
      _.each(@.children, (view, el) ->
        view.setElement(@.$(el))
        view.$el.addClass(view.className) if view.className isnt null

        # Transfer this view to the @.innerViews property
        @.innerViews[el] = view

        # Ensure this isn't kept laying about now it has been added
        delete @.children[el]
      , @)

      # Loop through each subview and also render those. We're rendering after
      # adding our parent view so our events bind fine.
      #
      # TODO: Do we need to call this here or can we call it before so we only
      # have one repaint?
      _.invoke(@.innerViews, 'render')


    # Closes the view by cleaning all events then removing the container from
    # the DOM.
    close: () ->

      # Ensure the innerViews property actually exists before we try invoking
      # and closing the view
      @.innerViews = @.innerViews || {}

      # Close all innerViews first; this will remove their event binds
      _.invoke(@.innerViews, 'close') if @.innerViews isnt {}

      # TODO: document?
      @.unbind()

      # This calls Backbone's dispose method which removes all delegated
      # events, model binds and collection binds.
      # It also removes the element from the DOM
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
