define(["app"], (app) ->

  MenuView = Backbone.View.extend({
    tagName: 'ul',

    id: 'menu',

    initialize: ->
      app.Sites.on('changeSite', (site) ->
        @.render()
      , @)

    render: () ->
      if app.currentSite
        $(@.el).html("
          <li id='menu-dashboard'><a href='/admin/'>Dashboard</a></li>
          <li id='menu-content'><a href='/admin/content'>Content</a></li>
          <li id='menu-theme'><a href='/admin/theme'>Theme</a></li>")
        $('#header-content').append(@.el)

  })

)
