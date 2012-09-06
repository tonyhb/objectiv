# Defines our app.
define(["jquery", "underscore", "backbone", "modernizr"], ($, _, Backbone, Modernizr) ->

  app = {
    api : '/api.json/v1/',
    currentSite : null
  }

  # Parsing should always use the response content and exclude metadata
  _.extend(Backbone.Collection.prototype, {
    parse : (resp, xhr) ->
      return {} if resp.metadata.status isnt 200

      resp.content
  })

  # Always return our App object.
  app
)
