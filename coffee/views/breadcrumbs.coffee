define(["app", "text!templates/breadcrumb.html"], (app, breadcrumbLink) ->

  BreadcrumbView = Backbone.View.extend({
    tagName: 'li',

    template: _.template(breadcrumbLink),

    initialize: (link, text) ->
      @.link = link
      @.text = text

    render: () ->
      @.$el.html(@.template({
        link: @.link,
        text: @.text
      }))


  })

)
