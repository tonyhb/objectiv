# This is a Site model, which contains data on a site in the CMS
define(["app"], (app) ->

	Site = Backbone.Model.extend({
		initialize: () ->
			# Creating a new instance of a site.
			console.log("A new site has been instantiated")

	})
)
