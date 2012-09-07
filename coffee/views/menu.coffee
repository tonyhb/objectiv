define(["app", "text!templates/menu.html"], (app, menuTemplate) ->

  MenuView = Backbone.View.extend({
    tagName: 'ul',

    id: 'menu',

    template: _.template(menuTemplate),

    initialize: ->
      app.Sites.on('changeSite', (site) ->
        @.render()
      , @)

    render: () ->
      if app.currentSite
        $(@.el).html(@.template())
        $('#header-content').append(@.el)

  })

)
