# Define our router, requiring our App dependency.
define(["app", "views/dashboard/main", "views/content/view", "views/themes/view", "views/themes/new"], (app, DashboardView, ContentView, ThemeView, NewThemeView) ->

  # Create the router
  Router = {
    Router : Backbone.Router.extend({
      routes: {
        "" : "showDashboard",
        "content" : "showContent",
        "themes" : "showTheme",
        "themes/new" : "newTheme",
        "settings" : "showSettings",
        "*actions" : "defaultRoute"
      },

      showDashboard : ->
        app.AppView.showPage(new DashboardView())

      showContent : ->
        app.AppView.showPage(new ContentView())

      showTheme : ->
        app.AppView.showPage(new ThemeView())

      newTheme : ->
        app.AppView.showPage(new NewThemeView())

      showSettings : ->

      defaultRoute : (e) ->
        return @.showDashboard() if e is "admin" # Admin without a forward slas (ie. /admin)

        console.log(e)
    })
  }

)
