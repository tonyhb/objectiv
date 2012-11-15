define(["app"], (app) ->

  ListView = Backbone.View.extend({
    className: 'list',
    events: {
      'click li': 'select'
    },

    initialize: (properties) ->
      @.innerViews = @.innerViews || {}

      @.title = @.make('div', { class: 'title' }, properties.title)
      @.footer = @.make('div', { class: 'footer' }, '<i class="icon-plus-circle"></i> Add a new item')
      @.list = @.make('ol')

      @.on('addNew', @.add)

    render: () ->
      # Remove the element from the DOM before manipulation to minimise repaints
      previousSibling = @.$el.prev()
      parentNode = @.el.parentNode
      parentNode.removeChild(@.el)

      # Manipulate our element
      @.$el.empty()

      @.$el.append(@.title, @.list, @.footer)

      # Add the element back to the DOM
      if previousSibling.length isnt 0
        $(previousSibling).after(@.el)
      else
        $(parentNode).append(@.el)

      # Configure events
      # @todo tidy so we don't have events in the render method
      that = @
      $(@.footer).on('click', () ->
        that.trigger("addNew", that.collection)
      )

      @

    unbind: () ->
      # Remove the custom event watching our 'footer' element
      $(@.footer).off('click')
      
      delete @.title
      delete @.footer
      delete @.list

    # Adds a new list item, which amounts to adding a new model to a collection.
    add: (e) ->
      @.$(@.list).append(@.make('li', null, "test"))

    select: (e) ->
      @.$('li.selected').removeClass('selected')
      @.$(e.target).addClass('selected')

  })

)
