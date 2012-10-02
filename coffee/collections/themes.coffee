define(["app", "models/theme"], (app, Theme) ->

  Themes = Backbone.Collection.extend({
    model: Theme,

    initialize: () ->
      # Set the URL with our site ID inside
      siteId = app.currentSite.get('_id')
      @.url = app.api + 'sites/' + siteId['$id'] + '/themes'

  })
)
