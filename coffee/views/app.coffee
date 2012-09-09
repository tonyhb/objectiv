define(["app", "views/menu", "views/search"], (app, MenuView, SearchView) ->

  # This is the core view for the app, and is the main entry point for other
  # views.
  AppView = Backbone.View.extend({
    el: 'body',

    innerViews: {},

    initialize: ->
      # Load the menu
      @.innerViews.MenuView = new MenuView()
      @.innerViews.SearchView = new SearchView()

      if app.Sites.length is 1
        app.Sites.setCurrentSite(app.Sites.models[0])
      else
        # @TODO Show a choice of sites
        # app.Sites.setCurrentSite(app.Sites.models[0])
        console.log app.Sites

    # Renders the initial app container. This is called at the start of app
    # intiialisation, in main.js
    render: ->
      _.each(@.innerViews, (view, name) ->
        view.render()
      )

    # Loads a CMS page.
    showPage: (view) ->
      # @TODO unbind current page's events etc.
      data = view.render()
      $('#content').html(data)

  })
)
