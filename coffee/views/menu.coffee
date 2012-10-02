define(["app", "text!templates/menu.html"], (app, menuTemplate) ->

  MenuView = Backbone.View.extend({
    tagName: 'ul',
    id: 'menu',

    template: _.template(menuTemplate),

    initialize: () ->
      app.Sites.on('changeSite', (site) ->
        @.render()
      , @)

      # Bind to all router events for menu clicks
      app.Router.on('all', (name) ->
        # Get the menu name from the route
        menu = name.replace('route:show', 'menu-').toLowerCase()

        # If the menu element exists highlight it. if not it's a subchild and
        # nothing needs to happen
        if (document.getElementById(menu))
          $('#menu .active').removeClass('active')
          $('#' + menu).addClass('active')

      )

    render: () ->
      if app.currentSite
        # @TODO Check each menu item for dropdowns and authorisation
        $(@.el).html(@.template())
        $('#header-content').append(@.el)

  })

)
