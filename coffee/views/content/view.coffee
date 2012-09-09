define(["app", "text!templates/content/view.html"], (app, contentTemplate) ->

  # @TODO extend page view class to abstract render etc. classes
  ContentView = Backbone.View.extend({
    template: _.template(contentTemplate)

    initialize: () ->

    events: {
    },

    render: () ->
      @.template()

  })

)
