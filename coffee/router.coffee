# Define our router, requiring our App dependency.
define(["app", "views/dashboard/main", "views/content/view"], (app, DashboardView, ContentView) ->

  # Create the router
  Router = {
    Router : Backbone.Router.extend({
      routes: {
        "" : "showDashboard",
        "content" : "showContent",
        "theme" : "showheme",
        "settings" : "showSettings",
        "*actions" : "defaultRoute"
      },

      showDashboard : ->
        # Dashboard

      showContent : ->

      showTheme : ->

      showSettings : ->

      defaultRoute : (e) ->
        return @.showDashboard() if e is "admin" # Admin without a forward slas (ie. /admin)

        console.log(e)
        console.log("default")
    })
  }

)
