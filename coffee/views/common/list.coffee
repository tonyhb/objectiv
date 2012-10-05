define(["app"], (app) ->

  ListView = Backbone.View.extend({
    className: 'list',
    events: {},

    initialize: (properties) ->
      @.innerViews = @.innerViews || {}
      @.title = @.make('div', { class: 'title' }, properties.title)
      @.footer = @.make('div', { class: 'footer' }, '<i class="icon-plus-circle"></i> Add a new item')

    render: () ->
      # Remove the element from the DOM before manipulation to minimise repaints
      previousSibling = @.$el.prev()
      parentNode = @.el.parentNode
      parentNode.removeChild(@.el)

      # Manipulate our element
      @.$el.empty()

      @.$el.append(@.title, @.footer)

      if previousSibling.length isnt 0
        $(previousSibling).after(@.el)
      else
        $(parentNode).append(@.el)

      @

  })

)
