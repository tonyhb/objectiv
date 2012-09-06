define(["app", "models/site"], (app, Site) ->

  Sites = Backbone.Collection.extend({
    model: Site,
    url: app.api + 'sites',

    setCurrentSite: (site) ->
      app.currentSite = site
      @.trigger("changeSite", site)

  })

)
