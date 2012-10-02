define(["app", "text!templates/search.html"], (app, searchTemplate) ->

  SearchView = Backbone.View.extend({
    tagName: 'div',
    id: 'omnibox',

    # Cache the template
    template: _.template(searchTemplate)

    # Cache the previous searched term. We use this to check to see if we need
    # to do any work
    term: '',

    initialize: () ->
      # Ensure this doens't go crazy with filtering - throttle what we can do at
      # any one point
      @.filter = _.debounce(@.filter, 300)

      app.Sites.on('changeSite', (site) ->
        @.rebuildCache(site)
      , @)

    unbind: () ->
      app.Sites.off('changeSite', null, @)

    events: {
      'keydown #omnibox_search': 'filter'
      'click #omnibox_search': 'filter'
    },

    render: () ->
      # Add the search box to the menu if we've got a site loaded
      if app.currentSite
        $(@.el).html(@.template)
      else
        $(@.el).html('')

      # Make sure this is added to the menu, too
      if @.el.parentNode is null
        $('#menu').after(@.el)

    filter: (evt) ->
      val = evt.target.value

      # Don't need to do anything - likely a ctrl press or something
      return if @.term is val

      # Update cached search term
      @.term = val

      # @TODO - Search collections and show reuslts
      console.log(val)

    rebuildCache: (site) ->
      # Rebuild the search cache for this site
  })

)
