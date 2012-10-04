define(["app", "collections/themes", "text!templates/themes/view.html", "text!templates/themes/view.blank.html"], (app, Themes, contentTemplate, blankSlateTemplate) ->

  # @TODO extend page view class to abstract render etc. classes
  ContentView = Backbone.View.extend({
    className: 'container',

    template: _.template(contentTemplate),
    blankSlateTemplate: _.template(blankSlateTemplate);

    initialize: () ->
      if app.cache.Themes is undefined
        app.cache.Themes = new Themes()

        # Load our models from the server
        app.cache.Themes.fetch()

    events: {
    },

    render: () ->
      # TODO: Show a blank slate with no collections or a list of collections
      if app.cache.Themes.length is 0
        @.$el.html(@.blankSlateTemplate({ siteName: app.currentSite.get('name') }))
      else
        @.$el.html(@.template())

  })

)

