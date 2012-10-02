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
        # Get the menu name from the URI
        menu = window.location.pathname.toLowerCase().replace('/admin/', '')

        # If this has a forward slash in it, get everything up to that point.
        # This will give us the main menu button to highlight
        menu = menu.substring(0, menu.indexOf('/')) if menu.indexOf('/') > 0

        # If it's the dasbhoard it will be empty...
        menu = "dashboard" if menu is ""

        # ID prefix
        menu = 'menu-' + menu

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
