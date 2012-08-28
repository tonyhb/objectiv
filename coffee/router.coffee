# Define our router, requiring our App dependency.
define(["app"], (app) ->

	# Create the router
	Router = {
		Router : Backbone.Router.extend({
			routes: {
				"" : "init",
				":site_id/dashboard" : "showDashboard",
				":site_id/content" : "showContent",
				":site_id/theme" : "showTheme",
				":site_id/settings" : "showSettings",
				"*actions" : "defaultRoute"
			},

			init : ->
				console.log('init')

			showDashboard : ->
				console.log("Dashboard")

			showContent : ->
				console.log("Content")

			showTheme : ->
				console.log("Theme")

			showSettings : ->
				console.log("Settings")

			defaultRoute : (e) ->
				console.log(e)
				console.log("default")
		})
	}

)
