define(["app"], (app) ->

	Menu = Backbone.View.extend({
		tagName: 'ul',
		id: 'menu',
		render: (event) ->
			$(@.el).html("
				<li id='menu-dashboard'><a href='/admin/'>Dashboard</a></li>
				<li id='menu-content'><a href='/admin/content'>Content</a></li>
				<li id='menu-theme'><a href='/admin/theme'>Theme</a></li>")
			$('#header-content').append(@.el)
	})

)
