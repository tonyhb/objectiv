define(["app", "text!templates/content/view.html"], (app, contentTemplate) ->

  # @TODO extend page view class to abstract render etc. classes
  ContentView = Backbone.View.extend({
    className: 'container',
    template: _.template(contentTemplate)

    initialize: () ->

    events: {},

    render: () ->
      @.$el.html(@.template())

  })

)
