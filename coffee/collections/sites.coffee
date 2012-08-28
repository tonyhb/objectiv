define(["app", "models/site"], (app, Site) ->

	Sites = Backbone.Collection.extend({
		model: Site,
		url: app.api + 'sites'
	})
)
