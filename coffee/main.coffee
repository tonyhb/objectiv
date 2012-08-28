require(["modernizr", "app", "router", "views/menu", "collections/sites"], (Modernizr, app, router, Menu, Sites) ->

	# Add the router to our main App
	app.Router = new router.Router()

	# Create the main menu before we begin our Router navigation.
	app.Menu = new Menu()
	app.Menu.render()

	# Find a list of sites we have access to
	app.Sites = new Sites()
	app.Sites.fetch()

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

	app.Router.navigate()
)
