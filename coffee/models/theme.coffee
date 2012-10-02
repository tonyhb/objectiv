define(["app"], (app) ->

  Theme = Backbone.Model.extend({
    initialize: () ->
      # Set the URL with our site ID inside
      siteId = app.currentSite.get('_id')
      @.url = app.api + 'sites/' + siteId['$id'] + '/themes'

    validate: (attr) ->
      console.log(attr)
  })

)
