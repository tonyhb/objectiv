# Define our router, requiring our App dependency.
define(["app"], (app) ->

  # Create the router
  Router = {
    Router : Backbone.Router.extend({
      routes: {
        "" : "init",
        "content" : "showContent",
        "theme" : "showTheme",
        "settings" : "showSettings",
        "*actions" : "defaultRoute"
      },

      init : ->
        # Dashboard
        $('#menu .active').removeClass('active')
        $('#menu-dashboard').addClass('active')

      showContent : ->
        $('#menu .active').removeClass('active')
        $('#menu-content').addClass('active')

      showTheme : ->
        $('#menu .active').removeClass('active')
        console.log("Theme")

      showSettings : ->
        $('#menu .active').removeClass('active')

      defaultRoute : (e) ->
        console.log(e)
        console.log("default")
    })
  }

)
