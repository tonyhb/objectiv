define(["app", "text!templates/dashboard.html"], (app, dashboardTemplate) ->

  # @TODO extend page view class to abstract render etc. classes
  DashboardView = Backbone.View.extend({
    template: _.template(dashboardTemplate)

    initialize: () ->

    events: {
    },

    render: () ->
      @.template()

  })

)
