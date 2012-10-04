define(["app"], (app) ->

  TopbarView = Backbone.View.extend({
    tagName: 'header', # This is always going to be a section/article header
    className: 'topbar',

    events: {
      "scroll" : "scroll"
    }

    initialize: () ->
      # Inner view initialisation
      @.innerViews = @.innerViews || {}

    render: () ->

      @.renderInnerViews()

    scroll: () ->
      console.log("scrolling")


  })

)
