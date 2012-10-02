require(["modernizr", "app", "router", "views/app", "collections/sites"], (Modernizr, app, router, AppView, Sites) ->

  # Testing
  window.app = app

  # Add the router to our main App and start.
  app.Router = new router.Router()

  # Use sites from pre-generated HTML
  app.Sites = new Sites()
  app.Sites.reset(Seed.sites)

  # Create our app. This will automatically set the site if we have one site
  # model in our collection.
  app.AppView = new AppView()
  app.AppView.render()

  if (Modernizr.history)
    # Use HTML5 pushstate
    Backbone.history.start({ pushState : true, root : "/admin/"})
    # And we now want to stop any proper links from working.
    $(document).on('click', 'a', (e) ->
      # Remove /admin/ from the href and navigate
      href = e.target.getAttribute('href').replace('/admin/', '')
      app.Router.navigate(href, { trigger: true })
      e.preventDefault()
      false
    )
  else
    Backbone.history.start({ root : "/admin/" })

  # Begin site navigation
  # Note: commented out because this always renavigates to root
  # app.Router.navigate()
)
