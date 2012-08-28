define(["app"], (app) ->

	Menu = Backbone.View.extend({
		tagName: 'ul',
		id: 'menu',
		render: (event) ->
			$(@.el).html("
				<li id='menu-home'><a href='/admin/'>Home</a></li>
				<li id='menu-content'><a href='/admin/content'>Content</a></li>")
			$('#header-content').append(@.el)
	})

)
