require(["modernizr", "app", "router", "views/menu"], (Modernizr, app, router, Menu) ->

	# Add the router to our main App
	app.router = new router.Router()

	# Create the menu
	app.menu = new Menu()

	app.menu.render()

	if (Modernizr.history)
		# Use HTML5 pushstate
		Backbone.history.start({ pushState : true, root : "/admin/"})

		# And we now want to stop any proper links from working.
		$(document).on('click', 'a', (e) ->
			# Remove /admin/ from the href and navigate
			href = e.target.getAttribute('href').replace('/admin/', '')
			app.router.navigate(href, { trigger: true })

			e.preventDefault()
			false
		)
	else
		Backbone.history.start({ root : "/admin/" })

	app.router.navigate()
)
