define(["app", "views/menu", "views/search", "views/breadcrumbs"], (app, MenuView, SearchView, BreadcrumbView) ->

  # This is the core view for the app, and is the main entry point for other
  # views.
  AppView = Backbone.View.extend({
    el: 'body',

    innerViews: {},
    breadcrumbs: [],

    initialize: ->
      # Load the menu
      @.innerViews.MenuView = new MenuView()
      @.innerViews.SearchView = new SearchView()

      # Now the menu is in place work out how high the main content should be.
      #@.render()
      app.height = @.$el.height() - (@.$('header.nav').height() + @.$('#breadcrumbs').height()) - 3
      $('#main').css('height',  app.height + 'px')

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
      # Close the current view to unbind events and remove nodes
      @.innerViews.ContentView.close() if @.innerViews.ContentView isnt undefined

      delete @.innerViews.ContentView

      # Add our content view and render it to the page
      @.innerViews.ContentView = view

      # Add the HTML to our wrapper
      $('#main').html(view.el)
      view.render()

      @

    clearBreadcrumbs: () ->

      # Close each view and delete the associated breadcrumb item
      _.each(@.breadcrumbs, (item, index) ->
        item.close()
        delete @.breadcrumbs[item]
      )

      @.breadcrumbs = []

      # Return this for chaining
      @

    addBreadcrumb: (params) ->
      # Use the current location by default
      params.link = window.location.pathname if params.link is undefined

      breadcrumb = new BreadcrumbView(params.link, params.text)
      @.breadcrumbs.push(breadcrumb)
      $('#breadcrumbs ol').append(breadcrumb.render())
      @

  })
)
