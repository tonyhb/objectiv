define(["app", "models/theme"], (app, Theme) ->

  HtmlCollection = Backbone.Collection.extend({
    initialize: () ->
      # Set the URL with our site ID inside
      siteId = app.currentSite.get('_id')
      @.url = app.api + 'sites/' + siteId['$id'] + '/themes/html'

  })
)
