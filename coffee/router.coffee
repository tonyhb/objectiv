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
          .clearBreadcrumbs()
          .addBreadcrumb({text: "Dashboard" })

      showContent : ->
        app.AppView.showPage(new ContentView())
          .clearBreadcrumbs()
          .addBreadcrumb({text: "Content" })

      showTheme : ->
        app.AppView.showPage(new ThemeView())
          .clearBreadcrumbs()
          .addBreadcrumb({text: "Themes" })

      newTheme : ->
        app.AppView.showPage(new NewThemeView())
          .addBreadcrumb({text: "New Theme" })

      showSettings : ->

      defaultRoute : (e) ->
        return @.showDashboard() if e is "admin" # Admin without a forward slas (ie. /admin)

        console.log(e)
    })
  }

)
