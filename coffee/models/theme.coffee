define((require) ->
  app = require("app")
  HtmlCollection    = require("collections/themes/html")
  CssCollection     = require("collections/themes/css")
  JsCollection      = require("collections/themes/js")
  ObjectsCollection = require("collections/themes/objects")
  ImagesCollection  = require("collections/themes/images")

  Theme = Backbone.Model.extend({
    initialize: () ->
      # Set the URL with our site ID inside
      siteId = app.currentSite.get('_id')
      @.url = app.api + 'sites/' + siteId['$id'] + '/themes'

      @.html = new HtmlCollection()
      @.css  = new CssCollection()
      @.js   = new JsCollection()
      @.objects = new ObjectsCollection()
      @.images  = new ImagesCollection()

    validate: (attr) ->
      console.log(attr)
  })

)
